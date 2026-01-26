@extends('layouts.app')

@section('title', 'Mobile Legends Top Up - '.config('app.name', 'Skins Collector'))
@section('body-class', 'ml-mobile-app')

@section('content')
<div class="ml-page-wrapper">
    <div class="container">
        {{-- HEADER --}}
        <div class="ml-header-mobile d-lg-none">
            <div class="ml-header-content">
                <img src="{{ asset('adminimages/' . ($gameImages['mlbb']->image_path ?? 'photo/sora.jpg')) }}" alt="MLBB" class="ml-header-icon" decoding="async" fetchpriority="high">
                <div>
                    <h1 class="ml-mobile-title">Mobile Legends</h1>
                    <p class="ml-mobile-subtitle">Instant Top Up</p>
                </div>
            </div>
        </div>

        <div class="row g-3">
            
            {{-- DESKTOP LEFT INFO (Hidden on Mobile) --}}
            <div class="col-lg-4 d-none d-lg-block">
                <div class="ml-card product-info-card">
                    <div class="ml-product-image-wrapper">
                        <img src="{{ asset('adminimages/' . ($gameImages['mlbb']->image_path ?? 'photo/sora.jpg')) }}" alt="Mobile Legends" class="ml-product-image" decoding="async" fetchpriority="high">
                    </div>
                    
                    <div class="ml-product-details">
                        <h3 class="ml-details-title">Instructions</h3>
                        <div class="ml-instruction-steps">
                            <div class="step-item">
                                <span class="step-num">1</span>
                                <span class="step-text">Enter User ID</span>
                            </div>
                            <div class="step-item">
                                <span class="step-num">2</span>
                                <span class="step-text">Select Diamonds</span>
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
                    <input type="hidden" name="game_type" value="mlbb">
                    
                    {{-- 1. REGION SELECTOR --}}
                    <div class="ml-card mb-3">
                        <div class="ml-card-header">
                            <span class="ml-step-circle">1</span>
                            <span class="ml-card-title">Select Region</span>
                        </div>
                        <div class="ml-card-body">
                            {{-- Region Selection with Radio Buttons (Auto-Submit) --}}
                            <div class="ml-region-selector">
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="myanmar" {{ $region === 'myanmar' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/mm.png" alt="Myanmar" class="ml-region-flag-img">
                                        <span class="ml-region-name">Myanmar</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="malaysia" {{ $region === 'malaysia' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/my.png" alt="Malaysia" class="ml-region-flag-img">
                                        <span class="ml-region-name">Malaysia</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="philippines" {{ $region === 'philippines' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/ph.png" alt="Philippines" class="ml-region-flag-img">
                                        <span class="ml-region-name">Philippines</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="singapore" {{ $region === 'singapore' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/sg.png" alt="Singapore" class="ml-region-flag-img">
                                        <span class="ml-region-name">Singapore</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="indonesia" {{ $region === 'indonesia' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/id.png" alt="Indonesia" class="ml-region-flag-img">
                                        <span class="ml-region-name">Indonesia</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="russia" {{ $region === 'russia' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/ru.png" alt="Russia" class="ml-region-flag-img">
                                        <span class="ml-region-name">Russia</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- 2. USER ID INPUT --}}
                    <div class="ml-card mb-3">
                        <div class="ml-card-header">
                            <span class="ml-step-circle">2</span>
                            <span class="ml-card-title">User Information</span>
                        </div>
                        <div class="ml-card-body">
                            <div class="row g-2">
                                <div class="col-7">
                                    <div class="form-floating ml-floating-input">
                                        <input type="tel" name="player_id" class="form-control" id="playerId" placeholder="User ID" required pattern="[0-9]*" inputmode="numeric">
                                        <label for="playerId">User ID</label>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-floating ml-floating-input">
                                        <input type="tel" name="zone_id" class="form-control" id="zoneId" placeholder="Zone ID" required pattern="[0-9]*" inputmode="numeric">
                                        <label for="zoneId">Zone ID</label>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-helper-text mt-2">
                                <i class="fas fa-info-circle"></i> Find ID in your game profile
                            </div>
                            
                            {{-- USERNAME DISPLAY --}}
                            <div id="usernameDisplay" class="mt-2" style="display: none;">
                                <div class="alert alert-info py-2 px-3 d-flex align-items-center mb-0" style="font-size: 0.9rem; border-radius: 8px;">
                                    <i class="fas fa-user-circle me-2"></i>
                                    <span>Username: <strong id="fetchedUsername"></strong></span>
                                </div>
                            </div>
                            <div id="usernameLoading" class="mt-2 text-muted small" style="display:none;">
                                <i class="fas fa-spinner fa-spin"></i> Checking ID...
                            </div>
                            <div id="usernameError" class="mt-2 text-danger small" style="display:none;">
                                <i class="fas fa-times-circle"></i> User account not found. Please input correct ID.
                            </div>
                        </div>
                    </div>

                    

                    {{-- 4. DIAMONDS GRID --}}
                    <div class="ml-card mb-5">
                        <div class="ml-card-header">
                            <span class="ml-step-circle">3</span>
                            <span class="ml-card-title">Select Items</span>
                        </div>
                        <div class="ml-card-body">
                            <div id="regionMismatchCard" class="mb-3" style="display:none;">
                                <div class="d-flex align-items-center" style="background-color:#fff8e1;border:1px solid #ffd54f;border-radius:12px;padding:12px;">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    <div class="me-3">
                                        <div class="fw-bold" style="color:#856404;">Region Mismatch</div>
                                        <div class="small" style="color:#6c757d;">Please buy from the same region.</div>
                                    </div>
                                    <button type="button" class="btn btn-warning btn-sm ms-auto" id="switchToUserRegionBtn">Switch region</button>
                                </div>
                            </div>
                            @php
                                $passProducts = $mlproducts->filter(function($p) {
                                    $n = strtolower($p->name);
                                    return str_contains($n, 'weekly pass') || str_contains($n, 'twilight pass');
                                });
                                
                                $frcProducts = $mlproducts->filter(function($p) {
                                    $n = strtolower($p->name);
                                    return str_contains($n, 'frc');
                                });
                                
                                $diamondProducts = $mlproducts->filter(function($p) {
                                    $n = strtolower($p->name);
                                    return !str_contains($n, 'weekly pass') && !str_contains($n, 'twilight pass') && !str_contains($n, 'frc');
                                });

                                $firstProduct = $passProducts->first() ?? ($frcProducts->first() ?? $diamondProducts->first());
                                $firstProductId = $firstProduct ? $firstProduct->product_id : null;
                            @endphp

                            @if($mlproducts->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p>No products available</p>
                                </div>
                            @else
                                {{-- 1. Weekly Pass & Twilight Pass --}}
                                @if($passProducts->isNotEmpty())
                                    <h6 class="category-header">
                                        <i class="fas fa-crown me-2"></i>Weekly & Twilight Pass
                                    </h6>
                                    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                                        @foreach($passProducts as $mlproduct)
                                            <div class="col">
                                                <input type="radio" class="btn-check" name="product_id" id="prod_{{ $mlproduct->product_id }}" value="{{ $mlproduct->product_id }}" data-price="{{ number_format($mlproduct->price) }} Ks" data-raw-price="{{ $mlproduct->price }}" {{ $mlproduct->product_id == $firstProductId ? 'checked' : '' }} required>
                                                <label class="product-btn w-100 h-100 d-flex flex-column justify-content-center py-3 text-center" for="prod_{{ $mlproduct->product_id }}">
                                                    <div class="check-circle">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <span class="product-name">{{ $mlproduct->name }}</span>
                                                    <span class="product-price">{{ number_format($mlproduct->price) }} Ks</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- 2. 2x Diamonds (FRC) --}}
                                @if($frcProducts->isNotEmpty())
                                    <h6 class="category-header">
                                        <i class="fas fa-gem me-2"></i>2x Diamonds (Promo)
                                    </h6>
                                    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                                        @foreach($frcProducts as $mlproduct)
                                            <div class="col">
                                                <input type="radio" class="btn-check" name="product_id" id="prod_{{ $mlproduct->product_id }}" value="{{ $mlproduct->product_id }}" data-price="{{ number_format($mlproduct->price) }} Ks" data-raw-price="{{ $mlproduct->price }}" {{ $mlproduct->product_id == $firstProductId ? 'checked' : '' }} required>
                                                <label class="product-btn w-100 h-100 d-flex flex-column justify-content-center py-3 text-center" for="prod_{{ $mlproduct->product_id }}">
                                                    <div class="check-circle">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <span class="product-name">{{ $mlproduct->name }}</span>
                                                    <span class="product-price">{{ number_format($mlproduct->price) }} Ks</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- 3. Other Diamonds --}}
                                @if($diamondProducts->isNotEmpty())
                                    <h6 class="category-header">
                                        <i class="fas fa-diamond me-2"></i>Regular Diamonds
                                    </h6>
                                    <div class="row row-cols-2 row-cols-md-4 g-3">
                                        @foreach($diamondProducts as $mlproduct)
                                            <div class="col">
                                                <input type="radio" class="btn-check" name="product_id" id="prod_{{ $mlproduct->product_id }}" value="{{ $mlproduct->product_id }}" data-price="{{ number_format($mlproduct->price) }} Ks" data-raw-price="{{ $mlproduct->price }}" {{ $mlproduct->product_id == $firstProductId ? 'checked' : '' }} required>
                                                <label class="product-btn w-100 h-100 d-flex flex-column justify-content-center py-3 text-center" for="prod_{{ $mlproduct->product_id }}">
                                                    <div class="check-circle">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <span class="product-name">{{ $mlproduct->name }}</span>
                                                    <span class="product-price">{{ number_format($mlproduct->price) }} Ks</span>
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

{{-- Hidden form for region switching --}}
<form id="regionSwitchForm" action="{{ route('mlproducts') }}" method="GET" style="display:none;">
    <input type="hidden" name="region" id="regionInput">
    <input type="hidden" name="player_id" id="hiddenPlayerId">
    <input type="hidden" name="zone_id" id="hiddenZoneId">
</form>

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
                <div class="mb-2"><strong>Zone ID:</strong> <span id="confirmServerId"></span></div>
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

<script>
    /* global bootstrap */
    
    window.changeRegion = function(region) {
        document.getElementById('regionInput').value = region;
        // Preserve user inputs
        document.getElementById('hiddenPlayerId').value = document.getElementById('playerId').value;
        document.getElementById('hiddenZoneId').value = document.getElementById('zoneId').value;
        
        document.getElementById('regionSwitchForm').submit();
    };

    document.addEventListener('DOMContentLoaded', function() {
        const productRadios = document.querySelectorAll('input[name="product_id"]');
        const footerTotal = document.getElementById('footerTotal');
        const currentRegion = "{{ $region }}";
        const orderForm = document.getElementById('orderForm');

        const submitBtn = document.getElementById('submitOrderBtn');
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const finalSubmitBtn = document.getElementById('finalSubmitBtn');

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

                if (userRegionNorm && userRegionNorm !== currentRegion) {
                    if (regionMismatchCard) {
                        regionMismatchCard.style.display = 'block';
                        regionMismatchCard.scrollIntoView({behavior:'smooth', block:'center'});
                    }
                    return;
                }

                const selectedProduct = document.querySelector('input[name="product_id"]:checked');
                if (!selectedProduct) {
                    alert('Please select a product.');
                    return;
                }

                // Show Confirm Modal
                const playerId = document.getElementById('playerId').value;
                const serverId = document.getElementById('zoneId').value;
                const label = document.querySelector(`label[for="${selectedProduct.id}"]`);
                const productName = label.querySelector('.product-name').textContent;
                const productPrice = label.querySelector('.product-price').textContent;

                document.getElementById('confirmPlayerId').textContent = playerId;
                document.getElementById('confirmServerId').textContent = serverId;
                document.getElementById('confirmItem').textContent = productName;
                document.getElementById('confirmPrice').textContent = productPrice;
                document.getElementById('confirmNickname').textContent = 'Checking...';
                
                confirmModal.show();
                if(finalSubmitBtn) finalSubmitBtn.disabled = true;

                // 6. Check ID inside Modal
                fetch('{{ route("ml.checkRole") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        game_id: playerId,
                        server_id: serverId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        document.getElementById('confirmNickname').textContent = data.username;
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
        let userRegionNorm = null;
        const regionMismatchCard = document.getElementById('regionMismatchCard');
        const switchToUserRegionBtn = document.getElementById('switchToUserRegionBtn');

        // USER ID CHECKER
        const playerIdInput = document.getElementById('playerId');
        const zoneIdInput = document.getElementById('zoneId');
        const usernameDisplay = document.getElementById('usernameDisplay');
        const fetchedUsername = document.getElementById('fetchedUsername');
        const usernameLoading = document.getElementById('usernameLoading');
        const usernameError = document.getElementById('usernameError');
        let debounceTimer; // no longer used for debounce
        let currentCheckController = null;

        // Numeric Input Sanitization
        const numericInputs = document.querySelectorAll('input[pattern="[0-9]*"]');
        numericInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        });

        // Player ID & Zone ID Listeners
        if (playerIdInput) {
            playerIdInput.addEventListener('input', function() {
                checkUserRole();
            });
        }
        if (zoneIdInput) {
            zoneIdInput.addEventListener('input', function() {
                checkUserRole();
            });
        }

        function checkUserRole() {
            const gameId = playerIdInput.value.replace(/[^0-9]/g, '');
            const serverId = zoneIdInput.value.replace(/[^0-9]/g, '');
            playerIdInput.value = gameId;
            zoneIdInput.value = serverId;

            if (gameId.length >= 4 && serverId.length >= 4) {
                usernameLoading.style.display = 'block';
                usernameDisplay.style.display = 'none';
                usernameError.style.display = 'none';

                if (currentCheckController) {
                    currentCheckController.abort();
                    currentCheckController = null;
                }
                currentCheckController = new AbortController();

                const controller = currentCheckController;
                const timeoutId = setTimeout(() => controller.abort(), 5000);
                fetch('{{ route("ml.checkRole") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    signal: currentCheckController.signal,
                    body: JSON.stringify({
                        game_id: gameId,
                        server_id: serverId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    usernameLoading.style.display = 'none';
                    currentCheckController = null;
                    clearTimeout(timeoutId);
                    if (data.status) {
                        usernameError.style.display = 'none';
                        fetchedUsername.textContent = '';
                        const nameText = document.createTextNode(data.username);
                        fetchedUsername.appendChild(nameText);
                        if (data.region && data.region !== 'Unknown') {
                            const regionBadge = document.createElement('span');
                            regionBadge.className = 'badge bg-primary ms-2';
                            regionBadge.textContent = data.region;
                            fetchedUsername.appendChild(regionBadge);
                            
                            const normalized = (function(s) {
                                const x = String(s).toLowerCase();
                                if (x.includes('myan') || x === 'mm' || x === 'mmk') return 'myanmar';
                                if (x.includes('malay') || x === 'my' || x === 'myr') return 'malaysia';
                                if (x.includes('phil') || x === 'ph' || x === 'php') return 'philippines';
                                if (x.includes('sing') || x === 'sg' || x === 'sgd') return 'singapore';
                                if (x.includes('indo') || x === 'id' || x === 'idr') return 'indonesia';
                                if (x.includes('rus') || x === 'ru' || x === 'rub') return 'russia';
                                return null;
                            })(data.region);
                            userRegionNorm = normalized;
                            if (normalized && normalized !== currentRegion) {
                                if (regionMismatchCard) {
                                    regionMismatchCard.style.display = 'block';
                                }
                            } else {
                                if (regionMismatchCard) {
                                    regionMismatchCard.style.display = 'none';
                                }
                            }
                        } else {
                            userRegionNorm = null;
                            if (regionMismatchCard) {
                                regionMismatchCard.style.display = 'none';
                            }
                        }

                        usernameDisplay.style.display = 'block';
                    } else {
                        usernameDisplay.style.display = 'none';
                        usernameError.style.display = 'block';
                    }
                })
                .catch((err) => {
                    usernameLoading.style.display = 'none';
                    if (err && err.name === 'AbortError') return;
                    currentCheckController = null;
                    clearTimeout(timeoutId);
                    usernameDisplay.style.display = 'none';
                    usernameError.style.display = 'none';
                });
            } else {
                usernameDisplay.style.display = 'none';
                usernameError.style.display = 'none';
            }
        }

        // Region change via event delegation (reduces multiple listeners)
        const regionSelector = document.querySelector('.ml-region-selector');
        if (regionSelector) {
            regionSelector.addEventListener('change', function(e) {
                const target = e.target;
                if (target && target.classList.contains('ml-region-input')) {
                    changeRegion(target.value);
                }
            });
        }

        if (switchToUserRegionBtn) {
            switchToUserRegionBtn.addEventListener('click', function() {
                if (userRegionNorm) {
                    const radio = document.querySelector('input.ml-region-input[value="'+userRegionNorm+'"]');
                    if (radio) radio.checked = true;
                    changeRegion(userRegionNorm);
                }
            });
        }
    });
</script>

@endsection

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-color: var(--primary);
        --primary-hover: var(--primary-dark);
        --bg-color: #f4f6f9; /* Soft Light Gray Background */
        --card-bg: #ffffff;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --border-color: #e9ecef;
        --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        --input-bg: #f8f9fa;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-color);
        background-attachment: scroll;
        color: var(--text-dark);
        min-height: 100vh;
    }

    /* Page Wrapper */
    .ml-page-wrapper {
        padding-top: 1.5rem;
        padding-bottom: 120px;
    }

    /* Header Mobile */
    .ml-header-mobile {
        margin-bottom: 1.5rem;
    }
    
    .ml-header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .ml-header-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .ml-mobile-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .ml-mobile-subtitle {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* Standard Card */
    .ml-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .ml-card-header {
        background: #fff;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
    }

    .ml-step-circle {
        width: 32px;
        height: 32px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 1rem;
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(var(--primary-rgb), 0.2);
    }

    .ml-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .ml-card-body {
        padding: 1.5rem;
    }

    /* Category Headers */
    .category-header {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--border-color);
        margin-top: 0.5rem;
    }
    
    .category-header i {
        color: var(--primary-color);
        margin-right: 10px;
        font-size: 1.1rem;
    }

    /* Product Buttons - Standard Style */
    .product-btn {
        background: #fff;
        border: 1px solid var(--border-color);
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
        border-color: var(--primary-color);
        background-color: #fcfcfc;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .btn-check:checked + .product-btn {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
        transform: translateY(-2px);
    }

    .product-name {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        line-height: 1.3;
        color: var(--text-dark);
    }

    .product-price {
        font-size: 0.85rem;
        color: var(--text-muted);
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
        color: var(--primary-color);
        font-size: 10px;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.2s ease;
    }

    .btn-check:checked + .product-btn .check-circle {
        opacity: 1;
        transform: scale(1);
        background: white;
        color: var(--primary-color);
    }
    
    /* Region Selector Standard */
    .ml-region-card {
        border: 1px solid var(--border-color);
        background: #fff;
        border-radius: 12px;
        padding: 0.5rem 0.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s;
        height: 100%;
        min-height: 70px;
        justify-content: center;
    }
    
    .ml-region-input:checked + .ml-region-card {
        border-color: var(--primary-color);
        background-color: rgba(var(--primary-rgb), 0.08);
        color: var(--primary-color);
        box-shadow: 0 0 0 1px var(--primary-color);
    }
    
    .ml-region-flag-img {
        width: 28px;
        height: auto;
        margin-bottom: 4px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .ml-region-name {
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.2;
    }
    
    .ml-check-icon {
        display: none; /* Hide old icon */
    }

    /* Forms */
    .form-control {
        background-color: var(--input-bg);
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }
    
    .form-control:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.15);
    }
    
    .form-floating > label {
        color: var(--text-muted);
    }
    
    .ml-helper-text {
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .ml-card-body {
            padding: 1rem;
        }
        .product-btn {
            padding: 0.75rem 0.5rem;
        }
        .ml-mobile-title {
            font-size: 1.25rem;
        }
    }
    /* Desktop Sidebar */
    .product-info-card {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .ml-product-image-wrapper {
        position: relative;
        padding-top: 60%;
        overflow: hidden;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
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
    
    .ml-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .ml-badge-instant {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(46, 204, 113, 0.95);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        z-index: 10;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .ml-details-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--text-dark);
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
        background: var(--primary-color);
        border-radius: 2px;
    }
    
    .step-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .step-num {
        background: rgba(var(--primary-rgb), 0.1);
        color: var(--primary-color);
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
/* Dynamic Price Display (No JS) */
    .total-amount::after {
        content: "0 Ks";
        font-weight: bold;
        color: var(--primary-color);
        font-size: 1.1rem;
    }
    
    @foreach($mlproducts as $p)
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
@endsection
