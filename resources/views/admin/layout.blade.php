<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Admin Dashboard') - {{ config('app.name', 'Skins Collector') }}</title>

    <!-- Bootstrap -->
    @php
        $bootstrapCssLocal = file_exists(public_path('vendor/bootstrap/css/bootstrap.min.css'));
        $fontAwesomeCssLocal = file_exists(public_path('vendor/fontawesome/css/all.min.css'));
    @endphp
    @if($bootstrapCssLocal)
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    @endif
    @if($fontAwesomeCssLocal)
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    @else
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('styles')
</head>
<body class="admin-body">

<div class="admin-sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="admin-shell">
    <aside class="admin-sidebar" id="sidebar">
        <div class="admin-sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <span class="admin-brand-mark">
                    <i class="fas fa-crown"></i>
                </span>
                <span class="admin-brand-text">Admin Panel</span>
            </a>
            <button class="btn admin-icon-btn d-md-none" type="button" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="admin-nav">
            <a href="{{ route('admin.confirm.orders') }}" class="admin-nav-link {{ request()->routeIs('admin.confirm.orders') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i>
                <span>Orders</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-images"></i>
                <span>Ads Manager</span>
            </a>
            <a href="{{ route('admin.game-images.index') }}" class="admin-nav-link {{ request()->routeIs('admin.game-images.*') ? 'active' : '' }}">
                <i class="fas fa-image"></i>
                <span>Game Images</span>
            </a>
            <a href="{{ url('admin/paymentmethod') }}" class="admin-nav-link {{ request()->is('admin/paymentmethod*') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i>
                <span>Payments</span>
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="admin-nav-link {{ request()->routeIs('admin.contacts.index') ? 'active' : '' }}">
                <i class="fas fa-address-book"></i>
                <span>Contact Manager</span>
            </a>
            <a class="admin-nav-link {{ request()->routeIs('admin.mlbb.prices', 'admin.pubg.prices', 'admin.mcgg.prices', 'admin.wwm.prices') ? '' : 'collapsed' }}" 
               data-bs-toggle="collapse" 
               href="#priceManagerSubmenu" 
               role="button" 
               aria-expanded="{{ request()->routeIs('admin.mlbb.prices', 'admin.pubg.prices', 'admin.mcgg.prices', 'admin.wwm.prices') ? 'true' : 'false' }}" 
               aria-controls="priceManagerSubmenu">
                <i class="fas fa-tags"></i>
                <span class="flex-grow-1">Product Price Manager</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.mlbb.prices', 'admin.pubg.prices', 'admin.mcgg.prices', 'admin.wwm.prices') ? 'show' : '' }}" id="priceManagerSubmenu">
                <div class="ps-3">
                    <a href="{{ route('admin.mlbb.prices') }}" class="admin-nav-link {{ request()->routeIs('admin.mlbb.prices') ? 'active' : '' }}">
                        <i class="fas fa-gem"></i>
                        <span>MLBB</span>
                    </a>
                    <a href="{{ route('admin.pubg.prices') }}" class="admin-nav-link {{ request()->routeIs('admin.pubg.prices') ? 'active' : '' }}">
                        <i class="fas fa-gun"></i>
                        <span>PUBG</span>
                    </a>
                    <a href="{{ route('admin.mcgg.prices') }}" class="admin-nav-link {{ request()->routeIs('admin.mcgg.prices') ? 'active' : '' }}">
                        <i class="fas fa-gem"></i>
                        <span>MCGG</span>
                    </a>
                    <a href="{{ route('admin.wwm.prices') }}" class="admin-nav-link {{ request()->routeIs('admin.wwm.prices') ? 'active' : '' }}">
                        <i class="fas fa-wind"></i>
                        <span>WWM</span>
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.cookieandapi') }}" class="admin-nav-link {{ request()->routeIs('admin.cookieandapi') ? 'active' : '' }}">
                <i class="fas fa-cookie-bite"></i>
                <span>Cookie and API Manager</span>
            </a>
            <a href="{{ route('admin.users') }}" class="admin-nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="{{ route('admin.activity.logs') }}" class="admin-nav-link {{ request()->routeIs('admin.activity.logs') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>User Activity</span>
            </a>
            <a href="{{ route('admin.bank.index') }}" class="admin-nav-link {{ request()->routeIs('admin.bank.index') ? 'active' : '' }}">
                <i class="fas fa-university"></i>
                <span>Bank & Profit</span>
            </a>
            <a href="{{ route('admin.error-logs.index') }}" class="admin-nav-link {{ request()->routeIs('admin.error-logs.*') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Error Logs</span>
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="admin-nav-link admin-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar sticky-top">
            <div class="container-fluid admin-topbar-inner">
                <button class="btn admin-icon-btn d-md-none" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="admin-topbar-title">
                    @yield('page_title', 'Admin Dashboard')
                </div>
                <div class="admin-topbar-user">
                    <span class="admin-user-name d-none d-sm-inline">
                        {{ auth()->user()->name ?? auth()->user()->email ?? 'Admin' }}
                    </span>
                    <span class="admin-user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </span>
                </div>
            </div>
        </header>

        <main class="admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@php
    $bootstrapJsLocal = file_exists(public_path('vendor/bootstrap/js/bootstrap.bundle.min.js'));
@endphp
@if($bootstrapJsLocal)
    <script defer src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@else
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
@endif
    <script defer src="{{ asset('js/admin.js') }}"></script>
@yield('scripts')

</body>
</html>
