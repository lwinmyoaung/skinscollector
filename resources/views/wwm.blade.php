@extends('layouts.app')

@section('title', 'WWM Top Up - '.config('app.name', 'Skins Collector'))
@section('body-class', 'ml-mobile-app')

@section('content')
<div class="ml-page-wrapper">
    <div class="container">
        @include('partials.game-header-info', [
            'gameKey' => 'wwm',
            'title' => 'Where Winds Meet',
            'steps' => ['Enter Player ID', 'Select Diamond Amount', 'Complete Payment'],
            'imageDefault' => 'photo/wwm.jpg'
        ])

            {{-- MAIN FORM AREA --}}
            <div class="col-lg-8">
                
                {{-- Alert messages handled by global alert component --}}


                <form method="POST" action="{{ route('payment.start') }}" id="orderForm">
                    @csrf
                    <input type="hidden" name="game_type" value="wwm">
                    <input type="hidden" name="region" value="global">
                    
                    {{-- 1. USER ID INPUT --}}
                    <div class="ml-card mb-3">
                        <div class="ml-card-header">
                            <span class="ml-step-circle">1</span>
                            <span class="ml-card-title">User Information</span>
                        </div>
                        <div class="ml-card-body">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-floating ml-floating-input">
                                        <input type="text" name="player_id" class="form-control" id="playerId" placeholder="Player ID" required>
                                        <label for="playerId">Player ID</label>
                                    </div>
                                    <div id="usernameDisplay" class="mt-2" style="display: none;">
                                        <div class="alert alert-info py-2 px-3 d-flex align-items-center mb-0" style="font-size: 0.9rem; border-radius: 8px;">
                                            <i class="fas fa-user-circle me-2"></i>
                                            <span>Username: <strong id="usernameText"></strong></span>
                                        </div>
                                    </div>
                                    <div class="ml-helper-text mt-2">
                                        <i class="fas fa-info-circle"></i> Find ID in your game profile
                                    </div>
                                    <div id="checkingLoader" class="mt-2" style="display: none;">
                                        <div class="text-primary d-flex align-items-center" style="font-size: 0.85rem;">
                                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                            <span>Verifying Player ID...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text text-white-50 mt-1">
                                <small>Enter your WWM ID.</small>
                            </div>
                        </div>
                    </div>

                    

                    {{-- 3. PRODUCTS GRID --}}
                    <div class="ml-card mb-5">
                        <div class="ml-card-header">
                            <span class="ml-step-circle">2</span>
                            <span class="ml-card-title">Select Items</span>
                        </div>
                        <div class="ml-card-body">
                            @php
                                $specialProducts = $products->filter(function($p) {
                                    $n = strtolower($p->name ?? '');
                                    return str_contains($n, 'pass') || 
                                           str_contains($n, 'weekly') || 
                                           str_contains($n, 'monthly') || 
                                           str_contains($n, 'fund') || 
                                           str_contains($n, 'pack');
                                });

                                $diamondPackages = $products->filter(function($p) use ($specialProducts) {
                                    if ($specialProducts->contains('product_id', $p->product_id)) return false;
                                    $n = strtolower($p->name ?? '');
                                    return preg_match('/^\d/', $p->name) || str_contains($n, 'jade') || str_contains($n, 'ingot') || str_contains($n, 'gold');
                                });

                                $otherPackages = $products->reject(function($p) use ($specialProducts, $diamondPackages) {
                                    return $specialProducts->contains('product_id', $p->product_id) || 
                                           $diamondPackages->contains('product_id', $p->product_id);
                                });

                                $firstProduct = $specialProducts->first() ?? $diamondPackages->first() ?? $otherPackages->first();
                                $firstProductId = $firstProduct ? $firstProduct->product_id : null;
                            @endphp

                            @if($products->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p>No products available</p>
                                </div>
                            @else
                                @include('partials.game-product-section', ['products' => $specialProducts, 'title' => 'Special Bundles & Packs', 'icon' => 'fas fa-star'])
                                @include('partials.game-product-section', ['products' => $diamondPackages, 'title' => 'Currency Packages', 'icon' => 'fas fa-gem'])
                                @include('partials.game-product-section', ['products' => $otherPackages, 'title' => 'Other Packages', 'icon' => 'fas fa-box'])
                            @endif

                            {{-- TOTAL & BUY BUTTON --}}
                            @include('partials.game-footer-action')
                        </div>
                    </div>

                    {{-- 3. PAYMENT (Hidden/Integrated) --}}
                    {{-- Payment button is now in the footer of product selection --}}

                </form>
            </div>
        </div>
    </div>
</div>

@include('partials.game-confirm-modal', ['gameKey' => 'wwm', 'itemSuffix' => '', 'priceSuffix' => 'Ks'])

@include('partials.game-styles')
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Price Update Logic - Handled by CSS



    const submitBtn = document.getElementById('submitOrderBtn');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');
    const form = document.getElementById('orderForm');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    @include('partials.game-scripts-common')

    // Check ID endpoint
    const checkIdUrl = "{{ route('wwm.checkId') }}";

    // Auto Check Logic (Debounced)
    const playerIdInput = document.getElementById('playerId');
    const usernameDisplay = document.getElementById('usernameDisplay');
    const usernameText = document.getElementById('usernameText');
    const checkingLoader = document.getElementById('checkingLoader');
    let typingTimer;
    let currentCheckController = null;

    playerIdInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        const playerId = this.value;

        if (currentCheckController) {
            currentCheckController.abort();
            currentCheckController = null;
        }

        usernameDisplay.style.display = 'none';

        if (playerId.length >= 4) {
            checkingLoader.style.display = 'block';
            currentCheckController = new AbortController();
            checkWwmId(playerId, currentCheckController);
        } else {
            checkingLoader.style.display = 'none';
        }
    });

    function checkWwmId(id, controller) {
        checkingLoader.style.display = 'block';
        const timeoutId = setTimeout(() => controller.abort(), 5000);

        fetch(checkIdUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            signal: controller.signal,
            body: JSON.stringify({
                game_id: id,
                server_id: ''
            })
        })
        .then(res => res.json())
        .then(data => {
            checkingLoader.style.display = 'none';
            currentCheckController = null;
            clearTimeout(timeoutId);
            if (data.result === 1) {
                let name = data.nickname || data.username || data.name || "Unknown";
                usernameText.textContent = name;
                usernameDisplay.style.display = 'block';
                usernameDisplay.querySelector('.alert').classList.remove('alert-danger');
                usernameDisplay.querySelector('.alert').classList.add('alert-info');
                usernameDisplay.querySelector('i').className = 'fas fa-user-circle me-2';
                
                if (typeof finalSubmitBtn !== 'undefined') finalSubmitBtn.disabled = false;
            } else {
                usernameText.textContent = "Player not found";
                usernameDisplay.style.display = 'block';
                usernameDisplay.querySelector('.alert').classList.remove('alert-info');
                usernameDisplay.querySelector('.alert').classList.add('alert-danger');
                usernameDisplay.querySelector('i').className = 'fas fa-times-circle me-2';
                
                if (typeof finalSubmitBtn !== 'undefined') finalSubmitBtn.disabled = true;
            }
        })
        .catch(err => {
            checkingLoader.style.display = 'none';
            clearTimeout(timeoutId);
            if (err && err.name === 'AbortError') return;
            currentCheckController = null;
            usernameText.textContent = "Error checking ID";
            usernameDisplay.style.display = 'block';
            usernameDisplay.querySelector('.alert').classList.remove('alert-info');
            usernameDisplay.querySelector('.alert').classList.add('alert-danger');
            usernameDisplay.querySelector('i').className = 'fas fa-exclamation-circle me-2';
        });
    }

    submitBtn.addEventListener('click', function() {
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const playerId = document.getElementById('playerId').value;
        const productRadio = document.querySelector('input[name="product_id"]:checked');
        
        if (!productRadio) {
            alert('Please select a diamond package.');
            return;
        }

        // Fill modal
        document.getElementById('confirmPlayerId').textContent = playerId;
        // document.getElementById('confirmServerId').textContent = serverId;
        
        // Find product details
        const label = document.querySelector(`label[for="${productRadio.id}"]`);
        const diamonds = label.querySelector('.product-name').textContent;
        // const price = label.querySelector('.product-price').textContent;

        const rawPrice = parseFloat(productRadio.dataset.rawPrice);
        const quantity = parseInt(qtyInput.value) || 1;
        const total = rawPrice * quantity;
        const formattedTotal = new Intl.NumberFormat().format(total);

        document.getElementById('confirmItem').textContent = diamonds;
        document.getElementById('confirmQuantity').textContent = quantity;
        document.getElementById('confirmPrice').textContent = formattedTotal;
        document.getElementById('confirmNickname').textContent = 'Checking...';

        confirmModal.show();

        // Check ID
        fetch(checkIdUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                game_id: playerId,
                server_id: ''
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.result === 1) {
                document.getElementById('confirmNickname').textContent = data.nickname;
                finalSubmitBtn.disabled = false;
            } else {
                document.getElementById('confirmNickname').innerHTML = '<span class="text-danger">Player Not Found</span>';
                finalSubmitBtn.disabled = true;
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('confirmNickname').innerHTML = '<span class="text-danger">Error checking ID</span>';
            finalSubmitBtn.disabled = true;
        });
    });

    finalSubmitBtn.addEventListener('click', function() {
        if (finalSubmitBtn.disabled || finalSubmitBtn.classList.contains('disabled')) return;
        finalSubmitBtn.disabled = true;
        finalSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
        form.submit();
    });
});
</script>
@endsection
