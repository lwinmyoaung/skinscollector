@props(['gameKey', 'title', 'subtitle' => 'Instant Top Up', 'steps' => ['Enter User ID', 'Select Items', 'Complete Payment'], 'imageDefault' => 'photo/sora.jpg'])

@php
    $dbPath = $gameImages[$gameKey]->image_path ?? null;
    $imagePath = ($dbPath && $dbPath !== '0') ? $dbPath : $imageDefault;
@endphp

{{-- HEADER --}}
<div class="ml-header-mobile d-lg-none">
    <div class="ml-header-content">
        <img src="{{ asset('adminimages/' . $imagePath) }}" alt="{{ $title }}" class="ml-header-icon" decoding="async" fetchpriority="high" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22100%22%20height%3D%22100%22%20viewBox%3D%220%200%20100%20100%22%3E%3Crect%20width%3D%22100%22%20height%3D%22100%22%20fill%3D%22%23e9ecef%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20fill%3D%22%236c757d%22%3E{{ substr($title, 0, 4) }}%3C%2Ftext%3E%3C%2Fsvg%3E'">
        <div>
            <h1 class="ml-mobile-title">{{ $title }}</h1>
            <p class="ml-mobile-subtitle">{{ $subtitle }}</p>
        </div>
    </div>
</div>

<div class="row g-3">
    
    {{-- DESKTOP LEFT INFO (Hidden on Mobile) --}}
    <div class="col-lg-4 d-none d-lg-block">
        <div class="ml-card product-info-card">
            <div class="ml-product-image-wrapper">
                <img src="{{ asset('adminimages/' . $imagePath) }}" alt="{{ $title }}" class="ml-product-image" decoding="async" fetchpriority="high" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22600%22%20height%3D%22400%22%20viewBox%3D%220%200%20600%20400%22%3E%3Crect%20width%3D%22600%22%20height%3D%22400%22%20fill%3D%22%23e9ecef%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20font-family%3D%22sans-serif%22%20font-size%3D%2230%22%20fill%3D%22%236c757d%22%3E{{ substr($title, 0, 4) }}%3C%2Ftext%3E%3C%2Fsvg%3E'">
            </div>
            
            <div class="ml-product-details">
                <h3 class="ml-details-title">Instructions</h3>
                <div class="ml-instruction-steps">
                    @foreach($steps as $index => $step)
                    <div class="step-item">
                        <span class="step-num">{{ $index + 1 }}</span>
                        <span class="step-text">{{ $step }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
