@extends('layouts.app')

@section('title', 'PUBG Mobile Top Up - '.config('app.name', 'Skins Collector'))
@section('body-class', 'ml-mobile-app')

@section('content')
<div class="ml-page-wrapper">
    <div class="container">
        @include('partials.game-header-info', [
            'gameKey' => 'pubg',
            'title' => 'PUBG Mobile',
            'steps' => ['Enter Player ID', 'Select UC Amount', 'Complete Payment'],
            'imageDefault' => 'photo/pubg.jpg'
        ])

            {{-- MAIN FORM AREA --}}
            <div class="col-lg-8">
                
                {{-- Alert messages handled by global alert component --}}


                <form method="POST" action="{{ route('payment.start') }}" id="orderForm">
                    @csrf
                    <input type="hidden" name="game_type" value="pubg">
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
                                        <input type="tel" name="player_id" class="form-control" id="playerId" placeholder="Player ID" required pattern="[0-9]*" inputmode="numeric">
                                        <label for="playerId">Player ID</label>
                                    </div>
                                    <div id="usernameDisplay" class="mt-2" style="display: none;">
                                        <div class="alert alert-info py-2 px-3 d-flex align-items-center mb-0" style="font-size: 0.9rem; border-radius: 8px;">
                                            <i class="fas fa-user-circle me-2"></i>
                                            <span>Username: <strong id="usernameText"></strong></span>
                                        </div>
                                    </div>
                                    <div id="checkingLoader" class="mt-2" style="display: none;">
                                        <div class="text-primary d-flex align-items-center" style="font-size: 0.85rem;">
                                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                            <span>Verifying Player ID...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-helper-text mt-2">
                                <i class="fas fa-info-circle"></i> Find ID in your game profile
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
                                $specialPackages = $products->filter(function($p) {
                                    $n = strtolower($p->name ?? '');
                                    return str_contains($n, 'royale pass') || 
                                           str_contains($n, 'elite pass') || 
                                           str_contains($n, 'prime') || 
                                           str_contains($n, 'weekly') || 
                                           str_contains($n, 'monthly') || 
                                           str_contains($n, 'pack') || 
                                           str_contains($n, 'plus');
                                });

                                $ucPackages = $products->filter(function($p) use ($specialPackages) {
                                    if ($specialPackages->contains('product_id', $p->product_id)) return false;
                                    $n = strtolower($p->name ?? '');
                                    return str_contains($n, 'uc');
                                });

                                $otherPackages = $products->reject(function($p) use ($specialPackages, $ucPackages) {
                                    return $specialPackages->contains('product_id', $p->product_id) || 
                                           $ucPackages->contains('product_id', $p->product_id);
                                });

                                $firstProduct = $specialPackages->first() ?? $ucPackages->first() ?? $otherPackages->first();
                                $firstProductId = $firstProduct ? $firstProduct->product_id : null;
                            @endphp

                            @if($products->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p>No products available</p>
                                </div>
                            @else
                                @include('partials.game-product-section', ['products' => $specialPackages, 'title' => 'Special Bundles & Packs', 'icon' => 'fas fa-star'])
                                @include('partials.game-product-section', ['products' => $ucPackages, 'title' => 'UC Packages', 'icon' => 'fas fa-coins'])
                                @include('partials.game-product-section', ['products' => $otherPackages, 'title' => 'Other Packages', 'icon' => 'fas fa-box'])
                            @endif

                            {{-- TOTAL & BUY BUTTON --}}
                            {{-- TOTAL & BUY BUTTON --}}
                            @include('partials.game-footer-action')
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@include('partials.game-confirm-modal', ['gameKey' => 'pubg', 'showZoneId' => false])

@include('partials.game-styles')

<script>
document.addEventListener('DOMContentLoaded', function() {
    @include('partials.game-scripts-common')
    const orderForm = document.getElementById('orderForm');
    const submitBtn = document.getElementById('submitOrderBtn');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    // Account Checking Logic Variables
    const playerIdInput = document.getElementById('playerId');
    const usernameDisplay = document.getElementById('usernameDisplay');
    const usernameText = document.getElementById('usernameText');
    const checkingLoader = document.getElementById('checkingLoader');
    let typingTimer;
    let currentCheckController = null;

    // Numeric Input Sanitization
    const numericInputs = document.querySelectorAll('input[pattern="[0-9]*"]');
    numericInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    });

    // Player ID Listener
    if (playerIdInput) {
        const triggerImmediateCheck = () => {
            const playerId = playerIdInput.value.replace(/[^0-9]/g, '');
            playerIdInput.value = playerId;

            if (currentCheckController) {
                currentCheckController.abort();
                currentCheckController = null;
            }

            usernameDisplay.style.display = 'none';

            if (playerId.length >= 4) {
                checkingLoader.style.display = 'block';
                currentCheckController = new AbortController();
                checkPubgId(playerId, currentCheckController);
            } else {
                checkingLoader.style.display = 'none';
            }
        };

        playerIdInput.addEventListener('input', triggerImmediateCheck);
        playerIdInput.addEventListener('blur', triggerImmediateCheck);
        playerIdInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                triggerImmediateCheck();
            }
        });
    }

    // Server ID Listener (if exists)
    const serverIdInput = document.getElementById('serverId');
    if (serverIdInput) {
        const triggerImmediateCheckFromServer = () => {
            const playerId = playerIdInput ? playerIdInput.value.replace(/[^0-9]/g, '') : '';
            if (playerIdInput) playerIdInput.value = playerId;

            if (currentCheckController) {
                currentCheckController.abort();
                currentCheckController = null;
            }

            usernameDisplay.style.display = 'none';

            if (playerId.length >= 4) {
                checkingLoader.style.display = 'block';
                currentCheckController = new AbortController();
                checkPubgId(playerId, currentCheckController);
            } else {
                checkingLoader.style.display = 'none';
            }
        };

        serverIdInput.addEventListener('input', triggerImmediateCheckFromServer);
        serverIdInput.addEventListener('blur', triggerImmediateCheckFromServer);
        serverIdInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                triggerImmediateCheckFromServer();
            }
        });
    }

    if (finalSubmitBtn) {
        finalSubmitBtn.addEventListener('click', function() {
            if (finalSubmitBtn.disabled || finalSubmitBtn.classList.contains('disabled')) return;
            finalSubmitBtn.disabled = true;
            finalSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            orderForm.submit();
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (!orderForm.checkValidity()) {
                orderForm.reportValidity();
                return;
            }

            const selectedProduct = document.querySelector('input[name="product_id"]:checked');
            if (!selectedProduct) {
                alert('Please select a product.');
                return;
            }

            // Show Confirm Modal
            const playerId = document.getElementById('playerId').value;
            const label = document.querySelector(`label[for="${selectedProduct.id}"]`);
            const productName = label.querySelector('.product-name').textContent;
            // const productPrice = label.querySelector('.product-price').textContent;

            const rawPrice = parseFloat(selectedProduct.dataset.rawPrice);
            const quantity = parseInt(qtyInput.value) || 1;
            const total = rawPrice * quantity;
            const formattedTotal = new Intl.NumberFormat().format(total) + ' Ks';

            document.getElementById('confirmPlayerId').textContent = playerId;
            document.getElementById('confirmItem').textContent = productName;
            document.getElementById('confirmQuantity').textContent = quantity;
            document.getElementById('confirmPrice').textContent = formattedTotal;
            document.getElementById('confirmNickname').textContent = 'Checking...';
            
            confirmModal.show();
            if(finalSubmitBtn) finalSubmitBtn.disabled = true;

            // 5. Check ID inside Modal
            fetch('{{ route("pubg.checkId") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ game_id: playerId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.result === 1) {
                    let name = data.nickname || data.username || data.name || "Unknown";
                    document.getElementById('confirmNickname').textContent = name;
                    if(finalSubmitBtn) finalSubmitBtn.disabled = false;
                } else {
                    document.getElementById('confirmNickname').innerHTML = '<span class="text-danger">Player Not Found</span>';
                    if(finalSubmitBtn) finalSubmitBtn.disabled = true;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('confirmNickname').innerHTML = '<span class="text-danger">Error checking ID</span>';
                if(finalSubmitBtn) finalSubmitBtn.disabled = true;
            });
        });
    }
    // -----------------------------



    // Account Checking Logic
    // Variables moved to top scope
    
    function checkPubgId(id, controller) {
        checkingLoader.style.display = 'block';
        const timeoutId = setTimeout(() => controller.abort(), 5000);

        fetch('{{ route("pubg.checkId") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            signal: controller.signal,
            body: JSON.stringify({ game_id: id })
        })
        .then(response => response.json())
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
            } else {
                usernameText.textContent = "Player not found";
                usernameDisplay.style.display = 'block';
                usernameDisplay.querySelector('.alert').classList.remove('alert-info');
                usernameDisplay.querySelector('.alert').classList.add('alert-danger');
                usernameDisplay.querySelector('i').className = 'fas fa-times-circle me-2';
            }
        })
        .catch(error => {
            checkingLoader.style.display = 'none';
            clearTimeout(timeoutId);
            if (error && error.name === 'AbortError') return;
            currentCheckController = null;
        });
    }
});
</script>
@endsection
