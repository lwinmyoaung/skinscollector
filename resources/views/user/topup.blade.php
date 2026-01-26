@extends('layouts.app')

@section('title', 'User Top Up')

@section('content')
<div class="container py-5 d-flex flex-column align-items-center justify-content-center">
    
    <div class="w-100 mb-4" style="max-width: 500px;">
        <div class="d-flex justify-content-start">
            <a href="{{ url('/') }}" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i> Go Back
            </a>
        </div>
    </div>

    <div class="wallet-card p-4 w-100 wallet-card-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
             <div>
                <h4 class="mb-0 fw-bold">Top Up Wallet</h4>
                <small class="opacity-75">Add funds to your account</small>
            </div>
            <i class="fas fa-credit-card wallet-icon wallet-icon-large"></i>
        </div>

        {{-- Alert messages handled by global alert component --}}


        <form method="POST" action="{{ route('user.topup.store') }}" enctype="multipart/form-data" class="mt-3">
            @csrf
            @if(request('redirect') || old('redirect'))
                <input type="hidden" name="redirect" value="{{ old('redirect', request('redirect')) }}">
            @endif

            {{-- Top Up Amount --}}
            <div class="mb-4">
                <label class="form-label text-white-50">ငွေပမာဏထည့်ပါ</label>
                <div class="input-group">
                    <span class="input-group-text border-0 bg-white text-dark fw-bold">MMK</span>
                    <input type="number" name="topupamount" class="form-control border-0 py-2"
                           placeholder="ငွေပမာဏထည့်ပါ" min="1" required>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="mb-4">
                <label class="form-label text-white-50">ငွေလွဲမည့်နည်းလမ်းရွေးပါ</label>
                <select name="payment_method" id="payment_method" class="form-select border-0 py-2" required>
                    <option value="">Select Payment Method</option>
                    @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->name }}"
                                data-image="{{ asset('adminimages/images/paymentmethodphoto/'.$pm->image) }}"
                                data-phone="{{ $pm->phone_number }}">
                            {{ $pm->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Payment Method Image --}}
            <div class="mb-4 d-none" id="payment_image_box">
                <label class="form-label text-white-50">Payment QR / Info</label>
                <div class="card p-3 border-0 bg-white payment-preview-card">
                    <img id="payment_image_preview" src="" alt="Payment Method Image" class="rounded payment-preview-img mb-3" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                    <div id="payment_phone_box" class="d-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="small text-muted">Phone Number</div>
                                <div id="payment_phone_text" class="fw-bold"></div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="copy_phone_btn">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transaction Screenshot --}}
            <div class="mb-4 d-none" id="transaction_box">
                <label class="form-label text-white-50">Transaction Screenshot</label>
                <div class="card bg-white border-dashed text-center p-4" onclick="document.getElementById('transaction_image').click()" style="cursor: pointer; border: 2px dashed #dee2e6;">
                    <div id="image-preview-container">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0 small">ငွေလွဲ Screenshotပုံ ပို့ပါ(id ပါရမည်)</p>
                    </div>
                    <input type="file" name="transaction_image" id="transaction_image" class="d-none" accept="image/*" onchange="previewTransactionImage(this)">
                    <img id="transaction-preview" src="#" alt="Preview" class="img-fluid mt-3 d-none" style="max-height: 200px; border-radius: 8px;">
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-grid mt-5">
                <button type="submit" class="btn btn-light text-primary fw-bold py-3 rounded-pill shadow-sm">
                    <i class="fas fa-check-circle me-2"></i>ငွေလွှဲခြင်းအတည်ပြုမည်
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment_method');
        const paymentImageBox = document.getElementById('payment_image_box');
        const paymentImagePreview = document.getElementById('payment_image_preview');
        const transactionBox = document.getElementById('transaction_box');
        const paymentPhoneBox = document.getElementById('payment_phone_box');
        const paymentPhoneText = document.getElementById('payment_phone_text');
        const copyBtn = document.getElementById('copy_phone_btn');

        paymentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const imageUrl = selectedOption.getAttribute('data-image');
            const phone = selectedOption.getAttribute('data-phone');

            if (imageUrl) {
                paymentImagePreview.src = imageUrl;
                paymentImageBox.classList.remove('d-none');
                transactionBox.classList.remove('d-none');
                if (phone && phone.trim() !== '') {
                    paymentPhoneText.textContent = phone;
                    paymentPhoneBox.classList.remove('d-none');
                } else {
                    paymentPhoneText.textContent = '';
                    paymentPhoneBox.classList.add('d-none');
                }
            } else {
                paymentImageBox.classList.add('d-none');
                transactionBox.classList.add('d-none');
                paymentPhoneBox.classList.add('d-none');
            }
        });

        copyBtn.addEventListener('click', function() {
            const phone = paymentPhoneText.textContent.trim();
            if (!phone) return;
            navigator.clipboard.writeText(phone).then(() => {
                copyBtn.textContent = 'Copied';
                setTimeout(() => copyBtn.textContent = 'Copy', 1500);
            }).catch(() => {});
        });
    });

    function previewTransactionImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('transaction-preview').src = e.target.result;
                document.getElementById('transaction-preview').classList.remove('d-none');
                document.getElementById('image-preview-container').classList.add('d-none');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    .btn-back {
        border: 2px solid #e9ecef;
        color: #6c757d;
        background: transparent;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-back:hover {
        border-color: #0d6efd;
        color: #0d6efd;
        background: transparent;
        transform: translateX(-5px);
    }
</style>
@endsection
