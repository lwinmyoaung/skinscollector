@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="mb-4 text-success">
                        <i class="fas fa-check-circle fa-5x"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Order Received!</h2>
                    <p class="text-muted mb-4">
                        Thank you for your order. We have received your payment proof and will process it shortly.
                        <br>
                        You will receive a notification once approved.
                    </p>
                    
                    <div class="d-grid gap-2 col-md-8 mx-auto">
                        <a href="{{ route('game.category') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i> Return to Home
                        </a>
                        <a href="{{ route('user.kpay.orders') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-history me-2"></i> View Order History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
