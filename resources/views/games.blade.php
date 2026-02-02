@extends('layouts.app')

@section('content')
    @if(count($advertiseSlides) > 0)
        <div id="homeHeroCarousel" class="carousel slide mg-home-carousel" data-bs-ride="carousel" data-bs-interval="3500">
            <div class="carousel-indicators">
                @foreach($advertiseSlides as $i => $slidePath)
                    <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" @if($i === 0) aria-current="true" @endif aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($advertiseSlides as $i => $slidePath)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        <img src="{{ asset('adminimages/'.$slidePath) }}" class="d-block w-100" alt="Promotion banner {{ $i + 1 }}" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev" aria-label="Previous">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next" aria-label="Next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    @endif

    <div class="products-grid-container">
        <!-- 4 Column Game Products Grid -->
        <div class="game-products-grid">
            <!-- Product 1 -->
            <div class="game-product-card" style="--card-index: 0">
                <div class="product-badges">
                    <span class="badge-featured">FEATURED</span>
                </div>
                
                <div class="quick-view" title="Quick View">
                    <i class="fas fa-eye"></i>
                </div>
                
                <div class="product-image-container">
                    <a href="{{ route('mlproducts') }}">
                        <img src="{{ asset('adminimages/' . ($gameImages->get('mlbb')?->image_path ?: 'photo/sora.jpg')) }}" 
                            alt="Mobile Legend:Bang Bang" class="product-image" decoding="async" fetchpriority="high" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22600%22%20height%3D%22400%22%20viewBox%3D%220%200%20600%22%20fill%3D%22%23e9ecef%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20font-family%3D%22sans-serif%22%20font-size%3D%2230%22%20fill%3D%22%236c757d%22%3EMLBB%3C%2Ftext%3E%3C%2Fsvg%3E'">
                    </a>
                    
                    <div class="platform-icons">
                        <span class="platform-icon" title="Mobile">
                            <i class="fas fa-mobile-alt"></i>
                        </span>
                    </div>
                </div>
                
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="{{ route('mlproducts') }}">Mobile Legends</a>
                    </h3>

                    <div class="product-actions">
                        <a href="{{ route('mlproducts') }}" class="btn-details">
                            <i class="fas fa-cart-plus"></i> ဝယ်ယူရန်
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="game-product-card" style="--card-index: 1">
                <div class="quick-view" title="Quick View">
                    <i class="fas fa-eye"></i>
                </div>
                
                <div class="product-image-container">
                    <a href="{{ route('mcgg') }}">
                        <img src="{{ asset('adminimages/' . ($gameImages->get('mcgg')?->image_path ?: 'photo/mcgg.jpg')) }}" 
                            alt="MCGG" class="product-image" decoding="async" fetchpriority="high" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22600%22%20height%3D%22400%22%20viewBox%3D%220%200%20600%20400%22%3E%3Crect%20width%3D%22600%22%20height%3D%22400%22%20fill%3D%22%23e9ecef%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20font-family%3D%22sans-serif%22%20font-size%3D%2230%22%20fill%3D%22%236c757d%22%3EMCGG%3C%2Ftext%3E%3C%2Fsvg%3E'">
                    </a>
                    <div class="platform-icons">
                        <span class="platform-icon" title="Mobile">
                            <i class="fas fa-mobile-alt"></i>
                        </span>
                    </div>
                </div>
                
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="{{ route('mcgg') }}">Magic Chess GoGo</a>
                    </h3>
                    
                    <div class="product-actions">
                        <a href="{{ route('mcgg') }}" class="btn-details">
                            <i class="fas fa-cart-plus"></i> ဝယ်ယူရန်
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="game-product-card" style="--card-index: 2">
                
                <div class="quick-view" title="Quick View">
                    <i class="fas fa-eye"></i>
                </div>
                
                <div class="product-image-container">
                    <a href="{{ route('pubg') }}">
                        <img src="{{ asset('adminimages/' . ($gameImages->get('pubg')?->image_path ?: 'photo/pubg.jpg')) }}" 
                            alt="PUBG Mobile (UC)" class="product-image" decoding="async" loading="lazy" onerror="this.src='https://placehold.co/600x400?text=PUBG'">
                    </a>
                    <div class="platform-icons">
                        <span class="platform-icon" title="Mobile">
                            <i class="fas fa-mobile-alt"></i>
                        </span>
                    </div>
                </div>
                
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="{{ route('pubg') }}">PUBG Mobile (UC)</a>
                    </h3>
                    
                    <div class="product-actions">
                        <a href="{{ route('pubg') }}" class="btn-details">
                            <i class="fas fa-cart-plus"></i> ဝယ်ယူရန်
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product 4 -->
            <div class="game-product-card" style="--card-index: 3">
                <div class="product-badges">
                    <span class="badge-new">NEW</span>
                </div>
                <div class="quick-view" title="Quick View">
                    <i class="fas fa-eye"></i>
                </div>
                
                <div class="product-image-container">
                    <a href="{{ route('wwm') }}">
                        <img src="{{ asset('adminimages/' . ($gameImages->get('wwm')?->image_path ?: 'photo/wwm.jpg')) }}" 
                            alt="WWM" class="product-image" decoding="async" loading="lazy" onerror="this.src='https://placehold.co/600x400?text=WWM'">
                    </a>
                    <div class="platform-icons">
                        <span class="platform-icon" title="Mobile">
                            <i class="fas fa-mobile-alt"></i>
                        </span>
                    </div>
                </div>
                
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="{{ route('wwm') }}">Where Winds Meet</a>
                    </h3>

                    <div class="product-actions">
                        <a href="{{ route('wwm') }}" class="btn-details">
                            <i class="fas fa-cart-plus"></i> ဝယ်ယူရန်
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
