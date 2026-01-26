@extends('layouts.app')

@section('title', 'My Wallet - '.config('app.name', 'Skins Collector'))

@section('styles')
<style>
    :root {
        --primary: #1c99dc;
        --primary-dark: #1578ad;
        --primary-light: #e0f2fe;
        --dark-text: #0b1b3a;
    }

    .back-btn {
        border: 0;
        color: #6c757d;
        background: #fff;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        width: fit-content;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .back-btn:hover {
        color: var(--primary);
        background: #fff;
        transform: translateX(-5px);
        box-shadow: 0 6px 15px rgba(28, 153, 220, 0.15);
    }
    
    .wallet-card {
        background: #ffffff;
        color: var(--dark-text);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(28, 153, 220, 0.15);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.8);
        max-width: 500px;
        position: relative;
    }
    
    /* Top accent bar */
    .wallet-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--primary), #0284c7);
    }

    .wallet-pattern-overlay {
        background-image: radial-gradient(var(--primary-light) 1.5px, transparent 1.5px);
        background-size: 24px 24px;
        opacity: 0.6;
    }

    .wallet-icon {
        font-size: 2.5rem;
        color: var(--primary);
        background: var(--primary-light);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        transform: rotate(-10deg);
        box-shadow: 0 10px 20px rgba(28, 153, 220, 0.1);
    }

    .balance {
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--dark-text) 0%, var(--primary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1;
        letter-spacing: -1px;
    }

    .currency {
        font-size: 1.2rem;
        color: #6c757d;
        font-weight: 600;
        vertical-align: top;
        margin-left: 8px;
        -webkit-text-fill-color: #6c757d;
    }

    .stats-badge {
        background: #f8faff;
        border-radius: 16px;
        padding: 15px 20px;
        min-width: 100px;
        border: 1px solid var(--primary-light);
        transition: all 0.2s ease;
    }
    
    .stats-badge:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(28, 153, 220, 0.1);
        border-color: var(--primary);
        background: #fff;
    }

    .stats-badge small {
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 600;
    }
    
    .stats-badge .fw-bold {
        font-size: 1.25rem;
        color: var(--dark-text);
        margin-top: 5px;
    }

    .btn-wallet {
        background: linear-gradient(135deg, var(--primary), #0284c7);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 16px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(28, 153, 220, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-wallet:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(28, 153, 220, 0.4);
        color: white;
    }
    
    .btn-wallet::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(255,255,255,0.2), transparent);
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .btn-wallet:hover::after {
        opacity: 1;
    }

    .icon-md {
        font-size: 1.2rem;
    }
</style>
@endsection

@section('content')
<div class="container py-5 d-flex flex-column align-items-center justify-content-center">

    {{-- Back Button --}}
    <a href="{{ url()->previous() }}" class="back-btn mb-5 d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left"></i>
        <span>Go Back</span>
    </a>

    {{-- Wallet Card --}}
    <div class="wallet-card p-5 w-100 position-relative">
        
        {{-- Background Pattern --}}
        <div class="position-absolute top-0 start-0 w-100 h-100 wallet-pattern-overlay">
        </div>

        <div class="position-relative z-2">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <small class="opacity-85 d-block mb-1">WELCOME TO</small>
                    <h4 class="mb-0 fw-bold" style="font-size: 22px;">Wallet</h4>
                </div>
                <i class="fas fa-wallet wallet-icon"></i>
            </div>



            {{-- Stats Badges --}}
            <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                <div class="stats-badge text-center">
                    <small class="d-block opacity-85 mb-1">Transactions</small>
                    <div class="fw-bold">{{ $transactionsCount ?? 0 }}</div>
                </div>
                <div class="stats-badge text-center">
                    <small class="d-block opacity-85 mb-1">Today</small>
                    <div class="fw-bold">{{ $todayTransactionsCount ?? 0 }}</div>
                </div>
                <div class="stats-badge text-center">
                    <small class="d-block opacity-85 mb-1">Status</small>
                    <div class="fw-bold text-success">
                        <i class="fas fa-check-circle me-1"></i> Active
                    </div>
                </div>
            </div>

            {{-- Top Up Button --}}
            <a href="{{ route('userwallet') }}" class="btn btn-wallet w-100 d-flex align-items-center justify-content-center gap-3">
                <i class="fas fa-plus-circle icon-md"></i>
                <span>Top Up</span>
                <i class="fas fa-arrow-right icon-md"></i>
            </a>

            {{-- Quick Info --}}
            <div class="mt-4 text-center">
                <small class="opacity-75 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-bolt text-warning"></i>
                    <span>Instant top-up • Secure payments</span>
                </small>
            </div>
        </div>
    </div>

    {{-- Bottom Note --}}
    <div class="mt-4 text-center">
        <small class="text-muted">
            <i class="fas fa-shield-alt me-1"></i>
            Secured • © {{ date('Y') }}
        </small>
    </div>

</div>
@endsection
