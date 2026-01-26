<!doctype html>
<html lang="en-US" class="n2webp">
<head>
    @if(app()->environment('production'))
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-S8S3L664M5"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-S8S3L664M5');
        </script>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5GDK3JK');</script>
    @endif

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="pingback" href="{{ url('/xmlrpc.php') }}">

    <!-- Favicons -->
    @php
        $iconDir = public_path('icon');
        $exists = function($p) { return file_exists($p); };
    @endphp
    @if($exists($iconDir.'/apple-touch-icon.png'))
        <link rel="apple-touch-icon" sizes="180x180" href="/icon/apple-touch-icon.png">
    @endif
    @if($exists($iconDir.'/favicon-32x32.png'))
        <link rel="icon" type="image/png" sizes="32x32" href="/icon/favicon-32x32.png">
    @endif
    @if($exists($iconDir.'/favicon-16x16.png'))
        <link rel="icon" type="image/png" sizes="16x16" href="/icon/favicon-16x16.png">
    @endif
    @if($exists($iconDir.'/favicon.ico'))
        <link rel="shortcut icon" href="/icon/favicon.ico">
    @endif
    @if($exists($iconDir.'/site.webmanifest'))
        <link rel="manifest" href="/icon/site.webmanifest">
    @endif
    @if($exists($iconDir.'/safari-pinned-tab.svg'))
        <link rel="mask-icon" href="/icon/safari-pinned-tab.svg" color="#f2a800">
    @endif
    <meta name="msapplication-TileColor" content="#f2a800">
    @if($exists($iconDir.'/browserconfig.xml'))
        <meta name="msapplication-config" content="/icon/browserconfig.xml">
    @endif
    <meta name="theme-color" content="#ffffff">

    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
    <style>img:is([sizes="auto" i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px }</style>
    
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- SEO Meta Tags -->
    <title>@yield('title', config('app.name', 'Skins Collector').' - Game Top Up & Digital Products')</title>
    <meta name="description" content="@yield('description', 'Game top ups and digital products with instant delivery')">
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title', config('app.name', 'Skins Collector'))" />
    <meta property="og:description" content="@yield('description', 'Game top ups and digital products with instant delivery')" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="{{ config('app.name', 'Skins Collector') }}" />
    <meta property="article:publisher" content="{{ url('/') }}" />

    <!-- Bootstrap CSS -->
    @php
        $bootstrapCssLocal = file_exists(public_path('vendor/bootstrap/css/bootstrap.min.css'));
        $fontAwesomeCssLocal = file_exists(public_path('vendor/fontawesome/css/all.min.css'));
    @endphp
    @if($bootstrapCssLocal)
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    @endif
    
    <!-- Font Awesome -->
    @if($fontAwesomeCssLocal)
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    @else
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    @endif
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/alert-card.css') }}">
    
    @yield('styles')
</head>

<body class="@yield('body-class', '')">
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5GDK3JK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

<!-- Alerts (Global) -->
@include('partials.alerts')

<div id="page" class="hfeed site">
    <!-- Header -->
    @include('partials.header')
    
    <!-- Main Content -->
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            @php
                $layoutEntryAdPath = null;
                if (request()->routeIs('game.category')) {
                    $entryFiles = \Illuminate\Support\Facades\Storage::disk('public')->files('ads/entry');
                    foreach ($entryFiles as $file) {
                        if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $file)) {
                            $layoutEntryAdPath = $file;
                            break;
                        }
                    }
                }
            @endphp
            @if($layoutEntryAdPath)
                <div id="layoutEntryAdOverlay" class="layout-entry-ad-overlay">
                    <div class="layout-entry-ad-backdrop"></div>
                    <div class="layout-entry-ad-container">
                        <button type="button" class="btn-close layout-entry-ad-close" aria-label="Close"></button>
                        <img src="{{ asset('adminimages/'.$layoutEntryAdPath) }}" alt="Advertisement" class="layout-entry-ad-image">
                    </div>
                </div>
                <style>
                    .layout-entry-ad-overlay {
                        position: fixed;
                        inset: 0;
                        z-index: 9999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .layout-entry-ad-backdrop {
                        position: absolute;
                        inset: 0;
                        background: rgba(0, 0, 0, 0.7);
                    }
                    .layout-entry-ad-container {
                        position: relative;
                        z-index: 1;
                        max-width: 90vw;
                        max-height: 90vh;
                        display: flex;
                        flex-direction: column;
                        align-items: flex-end;
                    }
                    .layout-entry-ad-close {
                        margin-bottom: 8px;
                        background-color: #ffc107;
                        border-radius: 999px;
                        padding: 6px;
                        opacity: 1;
                    }
                    .layout-entry-ad-image {
                        max-width: 100%;
                        max-height: 80vh;
                        height: auto;
                        border-radius: 12px;
                        object-fit: contain;
                        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
                    }
                    @media (max-width: 576px) {
                        .layout-entry-ad-image {
                            max-height: 75vh;
                        }
                    }
                </style>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        try {
                            var key = 'layoutEntryAdShown_v1';
                            var overlay = document.getElementById('layoutEntryAdOverlay');
                            if (!overlay) return;
                            if (sessionStorage.getItem(key)) {
                                overlay.style.display = 'none';
                                return;
                            }
                            function hideOverlay() {
                                overlay.style.display = 'none';
                                sessionStorage.setItem(key, '1');
                            }
                            var closeBtn = overlay.querySelector('.layout-entry-ad-close');
                            if (closeBtn) {
                                closeBtn.addEventListener('click', hideOverlay);
                            }
                            overlay.addEventListener('click', function(e) {
                                if (e.target === overlay) {
                                    hideOverlay();
                                }
                            });
                        } catch (e) {
                        }
                    });
                </script>
            @endif
            @yield('breadcrumb')
            
            @hasSection('sidebar')
                <div class="row">
                    <main id="main" class="site-main col-lg-9" role="main">
                        @yield('content')
                    </main>
                    
                    <!-- Sidebar -->
                    <aside class="col-lg-3">
                        @yield('sidebar')
                    </aside>
                </div>
            @else
                <main id="main" class="site-main" role="main">
                    @yield('content')
                </main>
            @endif
        </div>
    </div>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Sticky Add to Cart -->
    @yield('sticky-cart')
</div>

<!-- Bootstrap JS -->
@php
    $bootstrapJsLocal = file_exists(public_path('vendor/bootstrap/js/bootstrap.bundle.min.js'));
@endphp
@if($bootstrapJsLocal)
    <script defer src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@else
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
@endif
<!-- jQuery -->
@php
    $jqueryLocal = file_exists(public_path('vendor/jquery/jquery.min.js'));
@endphp
@if($jqueryLocal)
    <script defer src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
@else
    <script defer src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        (function() {
            function loadScript(url, attrs) {
                var s = document.createElement('script');
                s.src = url;
                s.defer = true;
                if (attrs) {
                    Object.keys(attrs).forEach(function(k){ s.setAttribute(k, attrs[k]); });
                }
                document.head.appendChild(s);
            }
            if (!window.jQuery) {
                loadScript('https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js', { crossorigin: 'anonymous', referrerpolicy: 'no-referrer' });
            }
            window.addEventListener('load', function() {
                if (!window.jQuery) {
                    loadScript('https://unpkg.com/jquery@3.7.1/dist/jquery.min.js', { crossorigin: 'anonymous', referrerpolicy: 'no-referrer' });
                }
            });
        })();
    </script>
@endif

@yield('scripts')

<!-- Custom Scripts -->
<script defer src="{{ asset('js/custom.js') }}"></script>

@if(app()->environment('production'))
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init','516890659902111');fbq('track','PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=516890659902111&ev=PageView&noscript=1"/></noscript>
@endif
</body>
</html>
