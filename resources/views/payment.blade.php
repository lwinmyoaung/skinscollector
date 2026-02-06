<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Complete Payment - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
        }
        
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-size: 1.1rem;
        }
        
        /* Truck Animation Styles - From Uiverse.io by vinodjangid07 - Modified */
        .loader {
            width: fit-content;
            height: fit-content;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .truckWrapper {
            width: 200px;
            height: 100px;
            display: flex;
            flex-direction: column;
            position: relative;
            align-items: center;
            justify-content: flex-end;
            overflow-x: hidden;
        }
        
        /* truck upper body */
        .truckBody {
            width: 130px;
            height: fit-content;
            margin-bottom: 6px;
            animation: motion 1s linear infinite;
        }
        
        /* truck suspension animation*/
        @keyframes motion {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(3px);
            }
            100% {
                transform: translateY(0px);
            }
        }
        
        /* truck's tires */
        .truckTires {
            width: 130px;
            height: fit-content;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0px 10px 0px 15px;
            position: absolute;
            bottom: 0;
        }
        
        .truckTires svg {
            width: 24px;
        }
        
        .road {
            width: 100%;
            height: 1.5px;
            background-color: #282828;
            position: relative;
            bottom: 0;
            align-self: flex-end;
            border-radius: 3px;
        }
        
        .road::before {
            content: "";
            position: absolute;
            width: 20px;
            height: 100%;
            background-color: #282828;
            right: -50%;
            border-radius: 3px;
            animation: roadAnimation 1.4s linear infinite;
            border-left: 10px solid white;
        }
        
        .road::after {
            content: "";
            position: absolute;
            width: 10px;
            height: 100%;
            background-color: #282828;
            right: -65%;
            border-radius: 3px;
            animation: roadAnimation 1.4s linear infinite;
            border-left: 4px solid white;
        }
        
        .lampPost {
            position: absolute;
            bottom: 0;
            right: -90%;
            height: 90px;
            animation: roadAnimation 1.4s linear infinite;
        }
        
        @keyframes roadAnimation {
            0% {
                transform: translateX(0px);
            }
            100% {
                transform: translateX(-350px);
            }
        }
        
        /* Custom styles for payment page */
        .payment-loader {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
        }
        
        .payment-loader.active {
            display: flex;
        }
        
        #paymentSubmitBtn {
            position: relative;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-text {
            transition: opacity 0.3s ease;
        }
        
        /* Hide button container when processing */
        .button-container.hidden {
            display: none !important;
        }
        
        /* Image upload preview */
        .image-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8f9fa;
        }
        
        .image-upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(13, 110, 253, 0.05);
        }
        
        /* Payment method box */
        #payment_method_box .card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .truckWrapper {
                width: 180px;
                height: 90px;
            }
            
            .lampPost {
                height: 70px;
            }
        }
        
        /* Alert customization */
        .alert-light {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            color: #495057;
        }
        
        /* Loading text animation */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        
        #loadingText {
            animation: pulse 1.5s infinite;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center py-4">
            <div class="col-lg-7 col-xl-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Complete Your Payment</span>
                            <span class="badge bg-white text-primary">Secure</span>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Order Summary --}}
                        <div class="alert alert-light border mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Item:</span>
                                <span class="fw-semibold">{{ $order['product_name'] ?? 'Product' }}</span>
                            </div>
                            @if(($order['quantity'] ?? 1) > 1)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Quantity:</span>
                                <span class="fw-semibold">{{ $order['quantity'] }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                <span class="fw-bold">Total Amount:</span>
                                <span class="fw-bold text-primary">{{ number_format($order['amount']) }} Ks</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('payment.submit') }}" enctype="multipart/form-data" id="paymentForm">
                            @csrf
                            <input type="hidden" name="game_type" value="{{ $order['game_type'] }}">
                            <input type="hidden" name="product_id" value="{{ $order['product_id'] }}">
                            <input type="hidden" name="product_name" value="{{ $order['product_name'] }}">
                            <input type="hidden" name="player_id" value="{{ $order['player_id'] }}">
                            <input type="hidden" name="server_id" value="{{ $order['server_id'] }}">
                            <input type="hidden" name="region" value="{{ $order['region'] }}">
                            <input type="hidden" name="quantity" value="{{ $order['quantity'] ?? 1 }}">

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">Select Payment Method</option>
                                    @foreach(($paymentMethods ?? []) as $pm)
                                        <option value="{{ strtolower($pm->name) }}"
                                                data-image="{{ asset('adminimages/images/paymentmethodphoto/' . $pm->image) }}"
                                                data-phone="{{ $pm->phone_number }}"
                                                {{ strtolower($pm->name) === 'kpay' ? 'selected' : '' }}>
                                            {{ $pm->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="payment_method_box" class="mt-3 d-none">
                                    <div class="card p-3 border-0 bg-light">
                                        <div class="d-flex flex-column flex-sm-row align-items-center gap-3 text-center text-sm-start">
                                            <!-- မူလကုဒ်အတိုင်း image ကိုပြန်ပြင်ထားပါတယ် -->
                                            <img id="payment_image_preview"
                                                 src=""
                                                 alt="Payment Method"
                                                 loading="lazy"
                                                 decoding="async"
                                                 class="rounded border img-fluid"
                                                 style="width: 250px; height: 250; object-fit: contain; "
                                             onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22400%22%20height%3D%22300%22%20viewBox%3D%220%200%20400%20300%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e9ecef%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20font-family%3D%22sans-serif%22%20font-size%3D%2220%22%20fill%3D%22%236c757d%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fsvg%3E'">
                                            <div class="w-100">
                                                <div class="fw-semibold" id="payment_method_name"></div>
                                                <div class="small text-muted d-flex flex-wrap align-items-center gap-2 mt-2">
                                                    <span style="color: black; font-size: 14px;">‌ငွေလွှဲရမည့်နံပါတ် : <span id="payment_phone_text" style="padding: 8px 5px; border-radius: 4px; background-color: #077bf0ff; color: white; font-size: 14px;"></span></span>
                                                    <button type="button" style="color: white;" class="btn btn-sm btn-outline-secondary btn-primary" id="copy_phone_btn">Copy</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="kpayFields">
                                <div class="mb-3">
                                    <label class="form-label">သင့်ဖုန်းနံပါတ်ထည့်ပါ</label>
                                    <input type="tel" name="kpay_phone" class="form-control" placeholder="09xxxxxxxxx" value="{{ auth()->user()->phone ?? '' }}" required>
                                </div>
                                <input type="hidden" name="amount" value="{{ $order['amount'] }}">
                                <div class="mb-3">
                                    <label class="form-label">‌ငွေလွှဲ Screenshotပုံ ထည့်ပါ။( id ပါရမည် )</label>
                                    <div class="card bg-light text-center p-4 border-0 image-upload-area"
                                         onclick="document.getElementById('transaction_image').click()"
                                         style="cursor: pointer; border: 2px dashed #dee2e6;">
                                        <div id="transaction-image-preview-container">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                            <p class="text-muted mb-0 small">ပုံပို့ရန် ဤနေရာကိုနှိပ်ပါ</p>
                                        </div>
                                        <input type="file"
                                               name="transaction_image"
                                               id="transaction_image"
                                               class="d-none"
                                               accept="image/*"
                                               required
                                               onchange="handleImageUpload(this)">
                                        <img id="transaction-image-preview"
                                             src="#"
                                             alt="Preview"
                                             class="img-fluid mt-3 d-none"
                                             style="max-height: 220px; border-radius: 10px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Button Container - Will be hidden when clicked -->
                            <div class="d-grid mt-4 button-container" id="buttonContainer">
                                <button type="submit" class="btn btn-primary btn-lg" id="paymentSubmitBtn">
                                    <span class="btn-text">‌ငွေပေးချေခြင်းအတည်ပြုမည်</span>
                                </button>
                            </div>
                            
                            <!-- Truck Animation Container - Hidden by default, shown when button is clicked -->
                            <div class="payment-loader" id="truckLoader">
                                <div class="loader">
                                    <div class="truckWrapper">
                                        <div class="truckBody">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 198 93" class="trucksvg">
                                                <path stroke-width="3" stroke="#282828" fill="#F83D3D" d="M135 22.5H177.264C178.295 22.5 179.22 23.133 179.594 24.0939L192.33 56.8443C192.442 57.1332 192.5 57.4404 192.5 57.7504V89C192.5 90.3807 191.381 91.5 190 91.5H135C133.619 91.5 132.5 90.3807 132.5 89V25C132.5 23.6193 133.619 22.5 135 22.5Z"></path>
                                                <path stroke-width="3" stroke="#282828" fill="#7D7C7C" d="M146 33.5H181.741C182.779 33.5 183.709 34.1415 184.078 35.112L190.538 52.112C191.16 53.748 189.951 55.5 188.201 55.5H146C144.619 55.5 143.5 54.3807 143.5 53V36C143.5 34.6193 144.619 33.5 146 33.5Z"></path>
                                                <path stroke-width="2" stroke="#282828" fill="#282828" d="M150 65C150 65.39 149.763 65.8656 149.127 66.2893C148.499 66.7083 147.573 67 146.5 67C145.427 67 144.501 66.7083 143.873 66.2893C143.237 65.8656 143 65.39 143 65C143 64.61 143.237 64.1344 143.873 63.7107C144.501 63.2917 145.427 63 146.5 63C147.573 63 148.499 63.2917 149.127 63.7107C149.763 64.1344 150 64.61 150 65Z"></path>
                                                <rect stroke-width="2" stroke="#282828" fill="#FFFCAB" rx="1" height="7" width="5" y="63" x="187"></rect>
                                                <rect stroke-width="2" stroke="#282828" fill="#282828" rx="1" height="11" width="4" y="81" x="193"></rect>
                                                <rect stroke-width="3" stroke="#282828" fill="#DFDFDF" rx="2.5" height="90" width="121" y="1.5" x="6.5"></rect>
                                                <rect stroke-width="2" stroke="#282828" fill="#DFDFDF" rx="2" height="4" width="6" y="84" x="1"></rect>
                                            </svg>
                                        </div>
                                        <div class="truckTires">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" class="tiresvg">
                                                <circle stroke-width="3" stroke="#282828" fill="#282828" r="13.5" cy="15" cx="15"></circle>
                                                <circle fill="#DFDFDF" r="7" cy="15" cx="15"></circle>
                                            </svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" class="tiresvg">
                                                <circle stroke-width="3" stroke="#282828" fill="#282828" r="13.5" cy="15" cx="15"></circle>
                                                <circle fill="#DFDFDF" r="7" cy="15" cx="15"></circle>
                                            </svg>
                                        </div>
                                        <div class="road"></div>
                                        <svg xml:space="preserve" viewBox="0 0 453.459 453.459" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Capa_1" version="1.1" fill="#000000" class="lampPost">
                                            <path d="M252.882,0c-37.781,0-68.686,29.953-70.245,67.358h-6.917v8.954c-26.109,2.163-45.463,10.011-45.463,19.366h9.993 c-1.65,5.146-2.507,10.54-2.507,16.017c0,28.956,23.558,52.514,52.514,52.514c28.956,0,52.514-23.558,52.514-52.514 c0-5.478-0.856-10.872-2.506-16.017h9.992c0-9.354-19.352-17.204-45.463-19.366v-8.954h-6.149C200.189,38.779,223.924,16,252.882,16 c29.952,0,54.32,24.368,54.32,54.32c0,28.774-11.078,37.009-25.105,47.437c-17.444,12.968-37.216,27.667-37.216,78.884v113.914 h-0.797c-5.068,0-9.174,4.108-9.174,9.177c0,2.844,1.293,5.383,3.321,7.066c-3.432,27.933-26.851,95.744-8.226,115.459v11.202h45.75 v-11.202c18.625-19.715-4.794-87.527-8.227-115.459c2.029-1.683,3.322-4.223,3.322-7.066c0-5.068-4.107-9.177-9.176-9.177h-0.795 V196.641c0-43.174,14.942-54.283,30.762-66.043c14.793-10.997,31.559-23.461,31.559-60.277C323.202,31.545,291.656,0,252.882,0z M232.77,111.694c0,23.442-19.071,42.514-42.514,42.514c-23.442,0-42.514-19.072-42.514-42.514c0-5.531,1.078-10.957,3.141-16.017 h78.747C231.693,100.736,232.77,106.162,232.77,111.694z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-3 text-muted" id="loadingText">Processing your payment...</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var paymentSelect = document.getElementById('payment_method');
            if (!paymentSelect) {
                return;
            }

            var box = document.getElementById('payment_method_box');
            var image = document.getElementById('payment_image_preview');
            var nameEl = document.getElementById('payment_method_name');
            var phoneText = document.getElementById('payment_phone_text');
            var copyBtn = document.getElementById('copy_phone_btn');
            var txInput = document.getElementById('transaction_image');
            var txPreview = document.getElementById('transaction-image-preview');
            var txContainer = document.getElementById('transaction-image-preview-container');

            function updatePaymentPreview() {
                if (!paymentSelect.options.length || !paymentSelect.value) {
                    box.classList.add('d-none');
                    image.src = '';
                    nameEl.textContent = '';
                    phoneText.textContent = '';
                    copyBtn.disabled = true;
                    return;
                }

                var selected = paymentSelect.options[paymentSelect.selectedIndex];
                var imageUrl = selected.getAttribute('data-image') || '';
                var phone = selected.getAttribute('data-phone') || '';

                // Show box if a payment method is selected, regardless of image/phone
                // This ensures the user sees the selection even if details are missing
                box.classList.remove('d-none');
                
                if (imageUrl) {
                    image.src = imageUrl;
                    image.classList.remove('d-none');
                } else {
                    // Use placeholder if no image
                    image.src = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22400%22%20height%3D%22300%22%20viewBox%3D%220%200%20400%20300%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e9ecef%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20font-family%3D%22sans-serif%22%20font-size%3D%2220%22%20fill%3D%22%236c757d%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fsvg%3E';
                    // Optional: image.classList.add('d-none'); if we want to hide it completely
                    image.classList.remove('d-none'); 
                }

                nameEl.textContent = selected.textContent.trim();

                if (phone && phone.trim() !== '') {
                    phoneText.textContent = phone;
                    phoneText.parentElement.classList.remove('d-none'); // Ensure parent span is visible
                    copyBtn.disabled = false;
                    copyBtn.classList.remove('d-none');
                } else {
                    phoneText.textContent = '';
                    phoneText.parentElement.classList.add('d-none'); // Hide parent span if no phone
                    copyBtn.disabled = true;
                    copyBtn.classList.add('d-none');
                }
            }

            paymentSelect.addEventListener('change', updatePaymentPreview);
            
            // Initialize preview on page load if a method is already selected
            if (paymentSelect.value) {
                updatePaymentPreview();
            }

            if (copyBtn) {
                copyBtn.addEventListener('click', function() {
                    var phone = phoneText.textContent.trim();
                    if (!phone) {
                        return;
                    }
                    navigator.clipboard.writeText(phone).then(function() {
                        var original = copyBtn.textContent;
                        copyBtn.textContent = 'Copied';
                        setTimeout(function() {
                            copyBtn.textContent = original;
                        }, 1500);
                    }).catch(function() {});
                });
            }

            // Form Submission Handling
            const paymentForm = document.getElementById('paymentForm');
            const submitBtn = document.getElementById('paymentSubmitBtn');
            const buttonContainer = document.getElementById('buttonContainer');
            const truckLoader = document.getElementById('truckLoader');
            const loadingText = document.getElementById('loadingText');

            if (paymentForm && submitBtn) {
                paymentForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    if (!this.checkValidity()) {
                        this.reportValidity();
                        return;
                    }

                    // Hide button container completely and show truck animation
                    buttonContainer.classList.add('hidden');
                    truckLoader.classList.add('active');
                    loadingText.textContent = 'Processing your payment...';

                    const formData = new FormData(this);
                    const fileInput = document.getElementById('transaction_image');

                    // Compress image if present
                    if (fileInput.files.length > 0) {
                        try {
                            loadingText.textContent = 'Compressing image...';
                            const compressedBlob = await compressImage(fileInput.files[0]);
                            formData.set('transaction_image', compressedBlob, 'compressed_image.jpg');
                        } catch (err) {
                            console.error('Compression failed:', err);
                        }
                    }

                    loadingText.textContent = 'Uploading payment details...';

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                try {
                                    const json = JSON.parse(text);
                                    throw new Error(json.message || response.statusText);
                                } catch (e) {
                                    throw new Error(`Server Error (${response.status})`);
                                }
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            loadingText.textContent = 'Payment successful! Redirecting...';
                            loadingText.style.color = 'var(--success-color)';
                            
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Submission failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        // Show error message and restore button
                        loadingText.textContent = 'Payment failed. Please try again.';
                        loadingText.style.color = 'var(--danger-color)';
                        
                        // Show button again after 3 seconds
                        setTimeout(() => {
                            buttonContainer.classList.remove('hidden');
                            truckLoader.classList.remove('active');
                            loadingText.textContent = 'Processing your payment...';
                            loadingText.style.color = 'var(--secondary-color)';
                        }, 3000);
                    });
                });
            }

            // Restore state on page show (bfcache)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    buttonContainer.classList.remove('hidden');
                    truckLoader.classList.remove('active');
                    loadingText.textContent = 'Processing your payment...';
                    loadingText.style.color = 'var(--secondary-color)';
                }
            });
        });

        function compressImage(file) {
            return new Promise((resolve, reject) => {
                const maxWidth = 1280;
                const maxHeight = 1280;
                const quality = 0.7;
                
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = event => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;
                        
                        if (width > maxWidth || height > maxHeight) {
                            if (width > height) {
                                height = Math.round(height * (maxWidth / width));
                                width = maxWidth;
                            } else {
                                width = Math.round(width * (maxHeight / height));
                                height = maxHeight;
                            }
                        }
                        
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        canvas.toBlob(blob => {
                            if (blob) {
                                resolve(blob);
                            } else {
                                reject(new Error('Canvas to Blob failed'));
                            }
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = error => reject(error);
                };
                reader.onerror = error => reject(error);
            });
        }

        function handleImageUpload(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Show preview immediately
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('transaction-image-preview').src = e.target.result;
                    document.getElementById('transaction-image-preview').classList.remove('d-none');
                    document.getElementById('transaction-image-preview-container').classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>