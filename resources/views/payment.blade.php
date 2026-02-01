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
                                        data-image="{{ asset('storage/images/paymentmethodphoto/' . $pm->image) }}"
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
                                         class="rounded border img-fluid"
                                         style="max-width: 300px; height:auto; object-fit:contain;"
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
                                       required>
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

            if (!imageUrl && !phone) {
                box.classList.add('d-none');
                image.src = '';
                nameEl.textContent = '';
                phoneText.textContent = '';
                copyBtn.disabled = true;
                return;
            }

            if (imageUrl) {
                image.src = imageUrl;
            }

            nameEl.textContent = selected.textContent.trim();

            if (phone && phone.trim() !== '') {
                phoneText.textContent = phone;
                copyBtn.disabled = false;
            } else {
                phoneText.textContent = '';
                copyBtn.disabled = true;
            }

            box.classList.remove('d-none');
        }

        paymentSelect.addEventListener('change', updatePaymentPreview);

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

        if (txInput && txPreview && txContainer) {
            txInput.addEventListener('change', function() {
                if (txInput.files && txInput.files[0]) {
                    // Check file size (10MB limit)
                    if (txInput.files[0].size > 10 * 1024 * 1024) {
                        alert('The image file is too large. Please upload an image smaller than 10MB.');
                        txInput.value = '';
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        txPreview.src = e.target.result;
                        txPreview.classList.remove('d-none');
                        txContainer.classList.add('d-none');
                    };
                    reader.readAsDataURL(txInput.files[0]);
                }
            });
        }

        updatePaymentPreview();

        // Handle Form Submission Loading State
        var paymentForm = document.getElementById('paymentForm');
        var submitBtn = document.getElementById('paymentSubmitBtn');
        
        if (paymentForm && submitBtn) {
            paymentForm.addEventListener('submit', function(e) {
                // If form is valid, show loading
                if (this.checkValidity()) {
                    var spinner = submitBtn.querySelector('.spinner-border');
                    var btnText = submitBtn.querySelector('.btn-text');
                    
                    if (spinner) spinner.classList.remove('d-none');
                    if (btnText) btnText.textContent = 'Processing...';
                    submitBtn.disabled = true;
                }
            });
        }

        // Reset button state if page is loaded from bfcache (back/forward cache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted && submitBtn) {
                var spinner = submitBtn.querySelector('.spinner-border');
                var btnText = submitBtn.querySelector('.btn-text');
                
                if (spinner) spinner.classList.add('d-none');
                if (btnText) btnText.textContent = '‌ငွေပေးချေခြင်းအတည်ပြုမည်';
                submitBtn.disabled = false;
            }
        });
    });
</script>
@endsection

@endsection