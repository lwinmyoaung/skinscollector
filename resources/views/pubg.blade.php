@extends('layouts.app')

@section('title', 'PUBG Mobile Top Up - '.config('app.name', 'Skins Collector'))
@section('body-class', 'ml-mobile-app')

@section('content')
<div class="ml-page-wrapper">
    <div class="container">
        {{-- HEADER --}}
        <div class="ml-header-mobile d-lg-none">
            <div class="ml-header-content">
                <img src="{{ asset('adminimages/' . ($gameImages['pubg']->image_path ?? 'photo/pubg.jpg')) }}" alt="PUBG Mobile" class="ml-header-icon" decoding="async" fetchpriority="high" onerror="this.src='https://cdn.midasbuy.com/images/apps/pubgm_100x100.png'">
                <div>
                    <h1 class="ml-mobile-title">PUBG Mobile</h1>
                    <p class="ml-mobile-subtitle">Instant Top Up</p>
                </div>
            </div>
        </div>

        <div class="row g-3">
            
            {{-- DESKTOP LEFT INFO (Hidden on Mobile) --}}
            <div class="col-lg-4 d-none d-lg-block">
                <div class="ml-card product-info-card">
                    <div class="ml-product-image-wrapper">
                        <img src="{{ asset('adminimages/' . ($gameImages['pubg']->image_path ?? 'photo/pubg.jpg')) }}" alt="PUBG Mobile" class="ml-product-image" decoding="async" fetchpriority="high" onerror="this.src='https://cdn.midasbuy.com/images/apps/pubgm_100x100.png'">
                    </div>
                    
                    <div class="ml-product-details">
                        <h3 class="ml-details-title">Instructions</h3>
                        <div class="ml-instruction-steps">
                            <div class="step-item">
                                <span class="step-num">1</span>
                                <span class="step-text">Enter Player ID</span>
                            </div>
                            <div class="step-item">
                                <span class="step-num">2</span>
                                <span class="step-text">Select UC Amount</span>
                            </div>
                            <div class="step-item">
                                <span class="step-num">3</span>
                                <span class="step-text">Complete Payment</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                                $isUcPackage = function ($p) {
                                    $name = (string) ($p->name ?? '');
                                    $category = (string) ($p->category ?? '');

                                    return preg_match('/\bUC\b/i', $name) || preg_match('/\bUC\b/i', $category);
                                };

                                $ucPackages = $products->filter($isUcPackage);
                                $otherPackages = $products->reject($isUcPackage);

                                $firstProduct = $ucPackages->first() ?? $otherPackages->first();
                                $firstProductId = $firstProduct ? $firstProduct->product_id : null;
                            @endphp

                            @if($products->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p>No products available</p>
                                </div>
                            @else
                                {{-- 1. UC Packages (Number Start) --}}
                                @if($ucPackages->isNotEmpty())
                                    <h6 class="category-header">
                                        <i class="fas fa-coins me-2"></i>UC Packages
                                    </h6>
                                    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                                        @foreach($ucPackages as $product)
                                            <div class="col">
                                                <input type="radio" class="btn-check" name="product_id" id="prod_{{ $product->product_id }}" value="{{ $product->product_id }}" data-price="{{ number_format($product->price) }} Ks" data-raw-price="{{ $product->price }}" {{ $product->product_id == $firstProductId ? 'checked' : '' }} required>
                                                <label class="product-btn w-100 h-100 d-flex flex-column justify-content-center py-3 text-center" for="prod_{{ $product->product_id }}">
                                                    <div class="check-circle">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <span class="product-name">{{ $product->name }}</span>
                                                    <span class="product-price">{{ number_format($product->price) }} Ks</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- 2. Other Packages (The Rest) --}}
                                @if($otherPackages->isNotEmpty())
                                    <h6 class="category-header">
                                        <i class="fas fa-box me-2"></i>Other Packages
                                    </h6>
                                    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                                        @foreach($otherPackages as $product)
                                            <div class="col">
                                                <input type="radio" class="btn-check" name="product_id" id="prod_{{ $product->product_id }}" value="{{ $product->product_id }}" data-price="{{ number_format($product->price) }} Ks" data-raw-price="{{ $product->price }}" {{ $product->product_id == $firstProductId ? 'checked' : '' }} required>
                                                <label class="product-btn w-100 h-100 d-flex flex-column justify-content-center py-3 text-center" for="prod_{{ $product->product_id }}">
                                                    <div class="check-circle">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <span class="product-name">{{ $product->name }}</span>
                                                    <span class="product-price">{{ number_format($product->price) }} Ks</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif

                            {{-- TOTAL & BUY BUTTON --}}
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center mobile-sticky-footer">
                                <div class="ml-footer-price">
                                    <span class="text-muted small">Total:</span>
                                    <div class="total-amount" id="footerTotal"></div>
                                </div>
                                <button type="button" class="ml-btn-buy" id="submitOrderBtn">
                                    ဝယ်ယူမည် <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- CONFIRMATION MODAL --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><strong>Nickname:</strong> <span id="confirmNickname" class="text-primary">Loading...</span></div>
                <div class="mb-2"><strong>Player ID:</strong> <span id="confirmPlayerId"></span></div>
                <div class="mb-2"><strong>Item:</strong> <span id="confirmItem"></span></div>
                <div class="mb-2"><strong>Price:</strong> <span id="confirmPrice"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="finalSubmitBtn">Confirm & Pay</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Reusing MLBB styles but ensuring they load */
:root {
    --ml-primary: var(--primary);
    --ml-primary-dark: var(--primary-dark);
    --ml-secondary: var(--primary-light);
    --ml-accent: var(--accent);
    --ml-bg: #f8f9fa;
    --ml-text: #2c3e50;
    --ml-card-bg: #ffffff;
    --ml-border-radius: 12px;
}

.ml-page-wrapper {
    background-color: var(--ml-bg);
    min-height: 100vh;
    padding-bottom: 120px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.ml-header-mobile {
    background: linear-gradient(135deg, var(--ml-primary), var(--ml-primary-dark));
    padding: 20px;
    border-radius: 0 0 20px 20px;
    margin-bottom: 20px;
    margin-left: -12px;
    margin-right: -12px;
    box-shadow: 0 4px 15px rgba(var(--primary-rgb), 0.2);
}

.ml-header-content {
    display: flex;
    align-items: center;
    color: white;
}

.ml-header-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    margin-right: 15px;
    border: 2px solid rgba(255,255,255,0.3);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.ml-mobile-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(244, 241, 241, 1);
}

.ml-mobile-subtitle {
    color: #e4d716ff;
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0;
}

.ml-card {
    background: var(--ml-card-bg);
    border-radius: var(--ml-border-radius);
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    overflow: hidden;
    height: 100%;
}

.ml-card-header {
    padding: 15px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    background-color: rgba(var(--primary-rgb), 0.03);
}

.ml-step-circle {
    background: var(--ml-primary);
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
    margin-right: 12px;
    box-shadow: 0 2px 5px rgba(var(--primary-rgb), 0.3);
}

.ml-card-title {
    font-weight: 600;
    color: var(--ml-text);
    font-size: 1rem;
}

.ml-card-body {
    padding: 20px;
}

/* Product Info Card */
.product-info-card {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.ml-product-image-wrapper {
    position: relative;
    padding-top: 60%;
    overflow: hidden;
}

.ml-product-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ml-product-details {
    padding: 25px;
}

.ml-details-title {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--ml-text);
    position: relative;
    padding-bottom: 10px;
}

.ml-details-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: var(--ml-primary);
    border-radius: 2px;
}

.step-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.step-num {
    background: rgba(var(--primary-rgb), 0.1);
    color: var(--ml-primary);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
    margin-right: 12px;
}

.step-text {
    font-size: 0.95rem;
    color: #555;
}

/* Form Inputs */
.ml-floating-input .form-control {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    height: 50px;
    font-size: 1rem;
}

.ml-floating-input .form-control:focus {
    border-color: var(--ml-primary);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.ml-helper-text {
    font-size: 0.85rem;
    color: #888;
}

/* Product Grid */
.category-header {
    font-size: 0.95rem;
    font-weight: 600;
    color: #ffd700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid rgba(0,0,0,0.05);
    margin-top: 0.5rem;
}

.category-header i {
    color: var(--ml-primary);
    margin-right: 10px;
    font-size: 1.1rem;
}

.product-btn {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 12px;
    padding: 1rem 0.5rem;
    position: relative;
    transition: all 0.2s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    text-align: center;
}

.product-btn:hover {
    border-color: var(--ml-primary);
    background-color: #fcfcfc;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

.btn-check:checked + .product-btn {
    background-color: var(--ml-primary);
    border-color: var(--ml-primary);
    color: white;
    box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
    transform: translateY(-2px);
}

.product-name {
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    line-height: 1.3;
    color: var(--ml-text);
}

.product-price {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
}

.btn-check:checked + .product-btn .product-name,
.btn-check:checked + .product-btn .product-price {
    color: white;
    font-weight: 600;
}

/* Check Circle Animation */
.check-circle {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 18px;
    height: 18px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ml-primary);
    font-size: 10px;
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.2s ease;
}

.btn-check:checked + .product-btn .check-circle {
    opacity: 1;
    transform: scale(1);
    background: white;
    color: var(--ml-primary);
}

/* Footer & Buy Button */
.ml-footer-price {
    display: flex;
    flex-direction: column;
}

.total-amount {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--ml-primary);
    line-height: 1.2;
}



/* Alerts */
.ml-alert {
    padding: 15px;
    border-radius: 8px;
    display: flex;
    align-items: start;
    font-size: 0.95rem;
}

.ml-alert-error {
    background-color: #fdecea;
    color: #dc3545;
    border: 1px solid #fadbd8;
}

.ml-alert-error i {
    margin-right: 10px;
    margin-top: 3px;
}
/* Dynamic Price Display */
.total-amount::after { content: "0 Ks"; }
@foreach($products as $p)
body:has(#prod_{{ $p->product_id }}:checked) .total-amount::after {
    content: "{{ number_format($p->price) }} Ks";
}
@endforeach

@media (max-width: 991px) {
    .mobile-sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 15px 20px;
        box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
        z-index: 9999;
        border-top: 1px solid #eee;
        margin-top: 0 !important;
        animation: slideUp 0.3s ease-out;
    }
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('orderForm');
    const submitBtn = document.getElementById('submitOrderBtn');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');

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
            const productPrice = label.querySelector('.product-price').textContent;

            document.getElementById('confirmPlayerId').textContent = playerId;
            document.getElementById('confirmItem').textContent = productName;
            document.getElementById('confirmPrice').textContent = productPrice;
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
