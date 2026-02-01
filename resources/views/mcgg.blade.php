@extends('layouts.app')

@section('title', 'MCGG Top Up - '.config('app.name', 'Skins Collector'))
@section('body-class', 'ml-mobile-app')

@section('content')
<div class="ml-page-wrapper">
    <div class="container">
        @include('partials.game-header-info', [
            'gameKey' => 'mcgg',
            'title' => 'MCGG',
            'steps' => ['Enter Player ID', 'Select Diamond Amount', 'Complete Payment'],
            'imageDefault' => 'photo/mcgg.jpg'
        ])

            {{-- MAIN FORM AREA --}}
            <div class="col-lg-8">
                
                {{-- Alert messages handled by global alert component --}}


                <form method="POST" action="{{ route('payment.start') }}" id="orderForm">
                    @csrf
                    <input type="hidden" name="game_type" value="mcgg">
                    <input type="hidden" name="region" value="global">
                    
                    {{-- 1. USER ID INPUT --}}
                    <div class="ml-card mb-3">
                        <div class="ml-card-header">
                            <span class="ml-step-circle">1</span>
                            <span class="ml-card-title">User Information</span>
                        </div>
                        <div class="ml-card-body">
                            <div class="row g-2">
                                <div class="col-8">
                                    <div class="form-floating ml-floating-input">
                                        <input type="text" name="player_id" class="form-control" id="playerId" placeholder="Player ID" required>
                                        <label for="playerId">Player ID</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-floating ml-floating-input">
                                        <input type="text" name="server_id" class="form-control" id="serverId" placeholder="Zone ID" required>
                                        <label for="serverId">Zone ID</label>
                                    </div>
                                </div>
                                <div class="col-12">
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
                                <i class="fas fa-info-circle"></i> Find ID and Zone ID in your game profile
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
                                           str_contains($n, 'fund');
                                });

                                $diamondProducts = $products->filter(function($p) use ($specialProducts) {
                                    if ($specialProducts->contains('product_id', $p->product_id)) return false;
                                    $n = strtolower($p->name ?? '');
                                    $c = strtolower($p->category ?? '');
                                    return $c === 'diamonds' || str_contains($n, 'diamond') || str_contains($n, 'coin');
                                });
                                
                                $otherProducts = $products->reject(function($p) use ($specialProducts, $diamondProducts) {
                                    return $specialProducts->contains('product_id', $p->product_id) || 
                                           $diamondProducts->contains('product_id', $p->product_id);
                                });

                                $firstProduct = $specialProducts->first() ?? $diamondProducts->first() ?? $otherProducts->first();
                                $firstProductId = $firstProduct ? $firstProduct->product_id : null;
                            @endphp

                            @if($products->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p>No products available</p>
                                </div>
                            @else
                                @include('partials.game-product-section', ['products' => $specialProducts, 'title' => 'Special Bundles & Packs', 'icon' => 'fas fa-star'])
                                @include('partials.game-product-section', ['products' => $diamondProducts, 'title' => 'Diamonds', 'icon' => 'fas fa-gem'])
                                @include('partials.game-product-section', ['products' => $otherProducts, 'title' => 'Packages & Others', 'icon' => 'fas fa-box'])
                            @endif

                            {{-- TOTAL & BUY BUTTON --}}
                            @include('partials.game-footer-action')
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@include('partials.game-styles')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Price Update Logic - Handled by CSS
    // const footerTotal = document.getElementById('footerTotal'); // Removed duplicate declaration
    
    @include('partials.game-scripts-common')

    // Account Checking Logic Variables
    const playerIdInput = document.getElementById('playerId');
    const serverIdInput = document.getElementById('serverId');
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

    // ID Check Listeners
    if (playerIdInput) {
        playerIdInput.addEventListener('input', triggerCheck);
    }
    if (serverIdInput) {
        serverIdInput.addEventListener('input', triggerCheck);
    }

    const orderForm = document.getElementById('orderForm');
    const submitBtn = document.getElementById('submitOrderBtn');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));


    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (!orderForm.checkValidity()) {
                orderForm.reportValidity();
                return;
            }

            const playerId = document.getElementById('playerId').value;
            const serverId = document.getElementById('serverId').value;
            const productRadio = document.querySelector('input[name="product_id"]:checked');
            
            if (!productRadio) {
                alert('Please select a package.');
                return;
            }

            document.getElementById('confirmPlayerId').textContent = playerId;
            document.getElementById('confirmServerId').textContent = serverId;
            
            const label = document.querySelector(`label[for="${productRadio.id}"]`);
            const productName = label.querySelector('.product-name').textContent;
            // const productPrice = label.querySelector('.product-price').textContent;

            const rawPrice = parseFloat(productRadio.dataset.rawPrice);
            const quantity = parseInt(qtyInput.value) || 1;
            const total = rawPrice * quantity;
            const formattedTotal = new Intl.NumberFormat().format(total) + ' Ks';

            document.getElementById('confirmItem').textContent = productName;
            document.getElementById('confirmQuantity').textContent = quantity;
            document.getElementById('confirmPrice').textContent = formattedTotal;
            document.getElementById('confirmNickname').textContent = 'Checking...';
            
            confirmModal.show();
            if(finalSubmitBtn) finalSubmitBtn.disabled = true;

            fetch('{{ route("mcgg.checkId") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ game_id: playerId, server_id: serverId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === true || data.result === 1) {
                    document.getElementById('confirmNickname').textContent = data.nickname || data.username || "Unknown";
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

    if (finalSubmitBtn) {
        finalSubmitBtn.addEventListener('click', function() {
            if (finalSubmitBtn.disabled || finalSubmitBtn.classList.contains('disabled')) return;
            finalSubmitBtn.disabled = true;
            finalSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            document.getElementById('orderForm').submit();
        });
    }

    // Account Checking Logic
    // Variables moved to top scope
    
    function triggerCheck() {
        const playerId = playerIdInput.value.replace(/[^0-9]/g, '');
        const serverId = serverIdInput.value.replace(/[^0-9]/g, '');
        playerIdInput.value = playerId;
        serverIdInput.value = serverId;

        if (currentCheckController) {
            currentCheckController.abort();
            currentCheckController = null;
        }

        usernameDisplay.style.display = 'none';

        if (playerId.length >= 4 && serverId.length >= 3) {
            checkingLoader.style.display = 'block';
            currentCheckController = new AbortController();
            checkMcggId(playerId, serverId, currentCheckController);
        } else {
            checkingLoader.style.display = 'none';
        }
    }

    // Input checks handled by single delegated listener on form

    function checkMcggId(id, zone, controller) {
        checkingLoader.style.display = 'block';
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        fetch('{{ route("mcgg.checkId") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            signal: controller.signal,
            body: JSON.stringify({ game_id: id, server_id: zone })
        })
        .then(response => response.json())
        .then(data => {
            checkingLoader.style.display = 'none';
            currentCheckController = null;
            clearTimeout(timeoutId);

            if (data.status === true || data.result === 1) {
                let name = data.nickname || data.username || data.name || "Unknown";
                usernameText.textContent = name;
                usernameDisplay.style.display = 'block';
                usernameDisplay.querySelector('.alert').classList.remove('alert-danger');
                usernameDisplay.querySelector('.alert').classList.add('alert-info');
                usernameDisplay.querySelector('i').className = 'fas fa-user-circle me-2';
            } else {
                // If the check returns result != 1 or error
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
            
            usernameText.textContent = "Error checking ID";
            usernameDisplay.style.display = 'block';
            usernameDisplay.querySelector('.alert').classList.remove('alert-info');
            usernameDisplay.querySelector('.alert').classList.add('alert-danger');
            usernameDisplay.querySelector('i').className = 'fas fa-exclamation-circle me-2';
        });
    }
});
</script>

@include('partials.game-confirm-modal', ['gameKey' => 'mcgg'])

@endsection
