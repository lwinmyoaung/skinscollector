@extends('layouts.app')

@section('content')
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
                            <div class="card bg-light text-center p-4 border-0"
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

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" id="paymentSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                            <span class="btn-text">‌ငွေပေးချေခြင်းအတည်ပြုမည်</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
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

        if (paymentForm && submitBtn) {
            paymentForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    this.reportValidity();
                    return;
                }

                const spinner = submitBtn.querySelector('.spinner-border');
                const btnText = submitBtn.querySelector('.btn-text');
                
                if (spinner) spinner.classList.remove('d-none');
                if (btnText) btnText.textContent = 'Compressing...';
                
                submitBtn.disabled = true;

                const formData = new FormData(this);
                const fileInput = document.getElementById('transaction_image');

                // Compress image if present
                if (fileInput.files.length > 0) {
                    try {
                        const compressedBlob = await compressImage(fileInput.files[0]);
                        formData.set('transaction_image', compressedBlob, 'compressed_image.jpg');
                        if (btnText) btnText.textContent = 'Uploading...';
                    } catch (err) {
                        console.error('Compression failed:', err);
                        if (btnText) btnText.textContent = 'Uploading (Original)...';
                    }
                } else {
                    if (btnText) btnText.textContent = 'Processing...';
                }

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
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
                        if (btnText) btnText.textContent = 'Success!';
                        // Optional: Show a success message or toast here
                        window.location.href = data.redirect_url;
                    } else {
                        throw new Error(data.message || 'Submission failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred. Please try again.');
                    
                    if (spinner) spinner.classList.add('d-none');
                    if (btnText) btnText.textContent = '‌ငွေပေးချေခြင်းအတည်ပြုမည်';
                    submitBtn.disabled = false;
                });
            });
        }

        // Restore button state on page show (bfcache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted && submitBtn) {
                const spinner = submitBtn.querySelector('.spinner-border');
                const btnText = submitBtn.querySelector('.btn-text');
                
                if (spinner) spinner.classList.add('d-none');
                if (btnText) btnText.textContent = '‌ငွေပေးချေခြင်းအတည်ပြုမည်';
                
                submitBtn.disabled = false;
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
@endsection
