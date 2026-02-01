@extends('layouts.app')

@section('title', 'Contact Us')

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
                <h1 class="fw-bold display-5 mb-3">Get in Touch</h1>
                <p class="lead text-muted">Have questions? We're here to help you.</p>
                <p class="lead text-muted">ပြဿနာတစ်စုံတစ်ရာရှိပါက အောက်ပါ Accountများသို့ ဆက်သွယ် မေးမြန်းနိုင်ပါတယ်ခင်ဗျာ။</p>
            </div>
            
            @include('partials.contact-info')

        </div>
    </div>
</div>
@endsection

<style>
    .btn-back {
        background: #0d6efd;
        color: #fff;
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .btn-back:hover {
        background: #0b5ed7;
        color: #fff;
        transform: translateX(-5px);
        box-shadow: 0 8px 15px rgba(13, 110, 253, 0.4);
        border-color: transparent;
    }
    .btn-back i {
        transition: transform 0.3s ease;
    }
    .btn-back:hover i {
        transform: translateX(-3px);
    }
</style>
