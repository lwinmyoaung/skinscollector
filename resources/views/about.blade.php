@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-start mb-4">
                <a href="{{ url('/') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Go Back
                </a>
            </div>

            <div class="text-center mb-5">
                <h1 class="fw-bold display-5 mb-3">About Us</h1>
                <p class="lead text-muted">Learn more about {{ config('app.name', 'Skins Collector') }}</p>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h4 class="fw-bold mb-4">Who We Are</h4>
                    <p class="text-muted mb-4">
                        {{ config('app.name', 'Skins Collector') }} is your premier destination for game top-ups and digital products. 
                        We provide a secure, fast, and reliable platform for gamers to enhance their gaming experience.
                    </p>

                    <h4 class="fw-bold mb-4">Our Mission</h4>
                    <p class="text-muted mb-4">
                        Our mission is to simplify digital payments for gamers worldwide. We strive to offer the best prices 
                        and the fastest delivery times in the market.
                    </p>

                    <h4 class="fw-bold mb-4">Why Choose Us?</h4>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Instant Delivery</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Secure Payments</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> 24/7 Support</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Competitive Prices</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
