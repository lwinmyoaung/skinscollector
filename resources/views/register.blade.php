@extends('layouts.app')

@section('title', 'Register - '.config('app.name', 'Skins Collector'))

@section('content')
<div class="container py-5 d-flex flex-column align-items-center justify-content-center auth-container">

    <div class="card shadow-lg p-4 w-100 border-0 rounded-4 auth-card">
        <div class="card-body">
            <h3 class="fw-bold text-center mb-4 text-primary">အကောင့်သစ်ပြုလုပ်ခြင်း</h3>
            
            {{-- Alert messages handled by global alert component --}}


            <form action="{{ route('register.store') }}" method="post">
                @csrf
                
                {{-- Email or Phone --}}
                <div class="mb-4">
                    <label for="identity" class="form-label fw-semibold">Email or Phone</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 ps-0" id="identity" name="identity" placeholder="ဖုန်းနံပါတ်(သို့)အီးမေးလ်ထည့်ပါ" required>
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" class="form-control bg-light border-start-0 ps-0" id="password" name="password" placeholder="Create a password" required>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary py-2 fw-bold rounded-pill">
                        Register
                    </button>
                </div>

                {{-- Login Link --}}
                <div class="text-center">
                    <span class="text-muted">အကောင့်ရှိပြီးပြီးလား?</span>
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold ms-1">Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
