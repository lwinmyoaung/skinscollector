@extends('layouts.app')

@section('title', 'Mobile Legends Top Up - '.config('app.name', 'Skins Collector'))
@section('body-class', 'ml-mobile-app')

@section('content')
<div class="ml-page-wrapper">
    <div class="container">
        @include('partials.game-header-info', [
            'gameKey' => 'mlbb',
            'title' => 'Mobile Legends',
            'steps' => ['Enter User ID', 'Select Diamonds', 'Complete Payment'],
            'imageDefault' => 'photo/sora.jpg'
        ])

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
                                        <img src="https://flagcdn.com/w40/mm.png" alt="Myanmar" class="ml-region-flag-img" loading="lazy">
                                        <span class="ml-region-name">Myanmar</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="malaysia" {{ $region === 'malaysia' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/my.png" alt="Malaysia" class="ml-region-flag-img" loading="lazy">
                                        <span class="ml-region-name">Malaysia</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="philippines" {{ $region === 'philippines' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/ph.png" alt="Philippines" class="ml-region-flag-img" loading="lazy">
                                        <span class="ml-region-name">Philippines</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="singapore" {{ $region === 'singapore' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/sg.png" alt="Singapore" class="ml-region-flag-img" loading="lazy">
                                        <span class="ml-region-name">Singapore</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="indonesia" {{ $region === 'indonesia' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/id.png" alt="Indonesia" class="ml-region-flag-img" loading="lazy">
                                        <span class="ml-region-name">Indonesia</span>
                                    </div>
                                </label>
                                <label class="ml-region-option">
                                    <input type="radio" name="region" class="ml-region-input" value="russia" {{ $region === 'russia' ? 'checked' : '' }}>
                                    <div class="ml-region-card">
                                        <i class="fas fa-check-circle ml-check-icon"></i>
                                        <img src="https://flagcdn.com/w40/ru.png" alt="Russia" class="ml-region-flag-img" loading="lazy">
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

                                $specialProducts = $mlproducts->filter(function($p) {
                                    $n = strtolower($p->name);
                                    return str_contains($n, 'super value') || 
                                           str_contains($n, 'epic') || 
                                           str_contains($n, 'monthly') || 
                                           str_contains($n, 'လစဥ်') || 
                                           str_contains($n, 'အပတ်စဥ်') || 
                                           str_contains($n, 'new products');
                                });
                                
                                $frcProducts = $mlproducts->filter(function($p) {
                                    $n = strtolower($p->name);
                                    return str_contains($n, 'frc') || str_contains($n, 'double');
                                });
                                
                                $diamondProducts = $mlproducts->filter(function($p) {
                                    $n = strtolower($p->name);
                                    return !str_contains($n, 'weekly pass') && 
                                           !str_contains($n, 'twilight pass') && 
                                           !str_contains($n, 'frc') && 
                                           !str_contains($n, 'double') &&
                                           !str_contains($n, 'super value') && 
                                           !str_contains($n, 'epic') && 
                                           !str_contains($n, 'monthly') && 
                                           !str_contains($n, 'လစဥ်') && 
                                           !str_contains($n, 'အပတ်စဥ်') && 
                                           !str_contains($n, 'new products');
                                });

                                $firstProduct = $passProducts->first() ?? ($specialProducts->first() ?? ($frcProducts->first() ?? $diamondProducts->first()));
                                $firstProductId = $firstProduct ? $firstProduct->product_id : null;
                            @endphp

                            @if($mlproducts->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p>No products available</p>
                                </div>
                            @else
                                {{-- 1. Weekly Pass & Twilight Pass --}}
                                @include('partials.game-product-section', ['products' => $passProducts, 'title' => 'Weekly & Twilight Pass', 'icon' => 'fas fa-crown'])

                                {{-- 2. Special Packs --}}
                                @include('partials.game-product-section', ['products' => $specialProducts, 'title' => 'Special Bundles & Packs', 'icon' => 'fas fa-star'])

                                {{-- 3. 2x Diamonds (FRC) --}}
                                @include('partials.game-product-section', ['products' => $frcProducts, 'title' => '2x Diamonds (Promo)', 'icon' => 'fas fa-gem'])

                                {{-- 4. Regular Diamonds --}}
                                @include('partials.game-product-section', ['products' => $diamondProducts, 'title' => 'Regular Diamonds', 'icon' => 'fas fa-diamond'])
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

{{-- Hidden form for region switching --}}
<form id="regionSwitchForm" action="{{ route('mlproducts') }}" method="GET" style="display:none;">
    <input type="hidden" name="region" id="regionInput">
    <input type="hidden" name="player_id" id="hiddenPlayerId">
    <input type="hidden" name="zone_id" id="hiddenZoneId">
</form>

@include('partials.game-confirm-modal', ['gameKey' => 'mobilelegend'])

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
        @include('partials.game-scripts-common')
        const currentRegion = "{{ $region }}";
        const orderForm = document.getElementById('orderForm');

        const submitBtn = document.getElementById('submitOrderBtn');
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const finalSubmitBtn = document.getElementById('finalSubmitBtn');

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
                
                const rawPrice = parseFloat(selectedProduct.dataset.rawPrice);
                const quantity = parseInt(qtyInput.value) || 1;
                const total = rawPrice * quantity;
                const formattedTotal = new Intl.NumberFormat().format(total) + ' Ks';

                document.getElementById('confirmPlayerId').textContent = playerId;
                document.getElementById('confirmServerId').textContent = serverId;
                document.getElementById('confirmItem').textContent = productName;
                document.getElementById('confirmQuantity').textContent = quantity;
                document.getElementById('confirmPrice').textContent = formattedTotal;
                document.getElementById('confirmNickname').textContent = 'Checking...';
                
                confirmModal.show();
                if(finalSubmitBtn) finalSubmitBtn.disabled = true;

                // 5. Check ID inside Modal
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
                    if (data.status === true || data.result === 1) {
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


        // Auto-Check Logic (Debounced)
        const playerIdInput = document.getElementById('playerId');
        const zoneIdInput = document.getElementById('zoneId');
        const usernameDisplay = document.getElementById('usernameDisplay');
        const usernameText = document.getElementById('usernameText');
        const checkingLoader = document.getElementById('checkingLoader');
        const regionMismatchCard = document.getElementById('regionMismatchCard');

        let typingTimer;
        let currentCheckController = null;
        let userRegionNorm = null; // Normalized region from API

        function triggerCheck() {
            const pid = playerIdInput.value.replace(/[^0-9]/g, '');
            const zid = zoneIdInput.value.replace(/[^0-9]/g, '');

            if (currentCheckController) {
                currentCheckController.abort();
                currentCheckController = null;
            }
            clearTimeout(typingTimer);

            usernameDisplay.style.display = 'none';
            if (regionMismatchCard) regionMismatchCard.style.display = 'none';
            userRegionNorm = null;

            if (pid.length >= 4 && zid.length >= 3) {
                checkingLoader.style.display = 'block';
                
                typingTimer = setTimeout(() => {
                    currentCheckController = new AbortController();
                    checkMlbbId(pid, zid, currentCheckController);
                }, 800);
            } else {
                checkingLoader.style.display = 'none';
            }
        }

        function checkMlbbId(pid, zid, controller) {
            checkingLoader.style.display = 'block';
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            fetch('{{ route("ml.checkRole") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ game_id: pid, server_id: zid }),
                signal: controller.signal
            })
            .then(res => res.json())
            .then(data => {
                checkingLoader.style.display = 'none';
                currentCheckController = null;
                clearTimeout(timeoutId);

                if (data.status === true || data.result === 1) {
                    let name = data.nickname || data.username || "Unknown";
                    usernameText.textContent = name;
                    usernameDisplay.style.display = 'block';
                    usernameDisplay.querySelector('.alert').classList.remove('alert-danger');
                    usernameDisplay.querySelector('.alert').classList.add('alert-info');
                    usernameDisplay.querySelector('i').className = 'fas fa-user-circle me-2';
                    
                    // Region Validation Logic
                    if (data.region) {
                        userRegionNorm = data.region.toLowerCase().trim(); 
                        
                        // Normalize common ISO codes and variations
                        const regionMap = {
                            'mm': 'myanmar',
                            'myanmar (burma)': 'myanmar',
                            'burma': 'myanmar',
                            'my': 'malaysia',
                            'ph': 'philippines',
                            'sg': 'singapore',
                            'id': 'indonesia',
                            'ru': 'russia',
                            'br': 'brazil',
                            'kh': 'cambodia',
                            'th': 'thailand',
                            'vn': 'vietnam'
                        };

                        if (regionMap[userRegionNorm]) {
                            userRegionNorm = regionMap[userRegionNorm];
                        }
                        
                        // Compare with selected region
                        if (currentRegion !== userRegionNorm) {
                            if (regionMismatchCard) {
                                regionMismatchCard.style.display = 'block';
                                regionMismatchCard.scrollIntoView({behavior:'smooth', block:'center'});
                            }
                        }
                    }

                } else {
                    usernameText.textContent = "Player not found";
                    usernameDisplay.style.display = 'block';
                    usernameDisplay.querySelector('.alert').classList.remove('alert-info');
                    usernameDisplay.querySelector('.alert').classList.add('alert-danger');
                    usernameDisplay.querySelector('i').className = 'fas fa-times-circle me-2';
                }
            })
            .catch((err) => {
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

        if (playerIdInput && zoneIdInput) {
            [playerIdInput, zoneIdInput].forEach(input => {
                input.addEventListener('input', triggerCheck);
            });
            
            // Trigger check on load if values exist (e.g. back button)
            if(playerIdInput.value && zoneIdInput.value) {
                triggerCheck();
            } else {
                usernameDisplay.style.display = 'none';
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

        const switchToUserRegionBtn = document.getElementById('switchToUserRegionBtn');
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
@include('partials.game-styles')
@endsection
