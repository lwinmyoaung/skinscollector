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
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
        background-color: #fff !important;
    }
    .transition-all {
        transition: all 0.2s ease;
    }
</style>
