@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
            </div>
            <h1 class="display-4 fw-bold mb-3">404</h1>
            <h2 class="h4 text-muted mb-4">Page Not Found</h2>
            <p class="lead mb-5">
                The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary px-4 py-2 fw-bold">
                <i class="fas fa-home me-2"></i> Return Home
            </a>
        </div>
    </div>
</div>
@endsection
