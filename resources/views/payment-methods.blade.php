@extends('layouts.app')

@section('title', 'Payment Methods')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-start mb-4">
                <a href="{{ url('/') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Go Back
                </a>
            </div>

            <div class="text-center mb-5">
                <h1 class="fw-bold display-5 mb-3">Payment Methods</h1>
                <p class="lead text-muted">We support a variety of secure payment options for your convenience.</p>
            </div>

            <div class="row g-4 justify-content-center">
                @forelse($paymentMethods as $method)
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body text-center p-4 d-flex flex-column align-items-center justify-content-center">
                                <div class="mb-3" style="width: 100%; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ $method->image_url }}" 
                                         alt="{{ $method->name }}" 
                                         class="img-fluid" 
                                         loading="lazy"
                                         decoding="async"
                                         style="max-width: 100%; height: auto; max-height: 220px; object-fit: contain;"
                                         onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22400%22%20height%3D%22300%22%20viewBox%3D%220%200%20400%20300%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e9ecef%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20font-family%3D%22sans-serif%22%20font-size%3D%2220%22%20fill%3D%22%236c757d%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fsvg%3E'">
                                </div>
                                @if(!empty($method->phone_number))
                                    <div class="mb-2 small text-muted">
                                        {{ $method->phone_number }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-credit-card fa-3x mb-3 opacity-50"></i>
                            <p>No payment methods listed currently.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 text-center">
                <p class="text-muted">
                    Need help with payments? <a href="{{ route('contact') }}" class="text-primary text-decoration-none fw-medium">Contact Support</a>
                </p>
            </div>
        </div>
    </div>
</div>

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
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection
