<header class="mg-header">
    <!-- Top Bar -->
    <div class="mg-topbar d-none d-lg-block">
        <div class="container">
            <div class="mg-topbar-content">
                <div class="mg-topbar-left">
                    <span class="mg-badge">🔥 Flash Sale Active</span>
                    <span class="mg-badge mg-badge-new">🎮 New Games Added</span>
                </div>
                
                @auth
                    @if(auth()->user()->role === 'admin')
                        <div class="mg-topbar-right d-flex align-items-center">
                            <a href="{{route('admin.dashboard')}}" class="mg-admin-badge">
                                <i class="fas fa-crown"></i> Admin Dashboard
                            </a>
                            <a href="{{ route('admin.kpay.orders') }}" class="mg-admin-badge ms-2" id="admin-orders-link">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Orders</span>
                                <span class="mg-nav-badge" id="admin-orders-count-badge" style="display: none;">0</span>
                            </a>
                        </div>
                    @elseif(!empty(auth()->user()->role))
                        <div class="mg-topbar-right">
                            <a href="#" class="mg-admin-badge mg-admin-badge-user">
                                <i class="fas fa-user-shield"></i> {{ auth()->user()->email }}
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="mg-main-header">
        <div class="container">
            <div class="mg-header-content">
                <!-- Logo -->
                <div class="mg-logo-container">
                    <a href="{{ url('/') }}" class="mg-logo">
                        <img src="{{ asset('adminimages/logo/skincollector.jpg') }}" alt="{{ config('app.name', 'Skins Collector') }}">
                        <span class="mg-logo-text d-none d-lg-block">{{ config('app.name', 'Skins Collector') }}</span>
                        <!-- Mobile Stacked Text -->
                        <div class="mg-mobile-brand d-lg-none">
                            <span class="mg-mobile-brand-top">Skins</span>
                            <span class="mg-mobile-brand-bottom">Collector</span>
                        </div>
                    </a>
                </div>
                
                <!-- Desktop User Actions -->
                <div class="mg-user-actions d-none d-lg-flex">
                    <!-- User Account -->
                    <div class="mg-user-dropdown">
                        <button class="mg-user-btn">
                            <i class="fas fa-user-circle"></i>
                            <span class="mg-user-text">
                                @auth
                                    {{ auth()->user()->name ?? auth()->user()->email }}
                                @else
                                    Account
                                @endauth
                            </span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="mg-dropdown-menu">
                            @auth
                                <div class="mg-user-info">
                                    <div class="mg-user-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="mg-user-details">
                                        <strong>{{ auth()->user()->name ?? auth()->user()->email }}</strong>
                                        <small>
                                            @if(auth()->user()->role === 'admin')
                                                <span class="mg-role-badge">Administrator</span>
                                            @elseif(!empty(auth()->user()->role))
                                                <span class="mg-role-badge">{{ ucfirst(auth()->user()->role) }}</span>
                                            @else
                                                Member
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="mg-dropdown-divider"></div>
                                <a href="{{ route('notifications.inbox') }}" class="mg-dropdown-item">
                                    <i class="fas fa-inbox"></i> Inbox
                                </a>
                                <a href="{{ route('user.kpay.orders') }}" class="mg-dropdown-item">
                                    <i class="fas fa-receipt"></i> My Orders
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <div class="mg-dropdown-divider"></div>
                                    <a href="{{route('admin.dashboard')}}" class="mg-dropdown-item mg-admin-link">
                                        <i class="fas fa-crown"></i> Admin Panel
                                    </a>
                                @endif
                                <div class="mg-dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}" class="mg-logout-form">
                                    @csrf
                                    <button type="submit" class="mg-dropdown-item mg-logout-btn">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            @else
                                <div class="mg-user-info">
                                    <div class="mg-user-details">
                                        <strong>Welcome!</strong>
                                        <small>No account needed to order</small>
                                    </div>
                                </div>
                                <div class="mg-dropdown-divider"></div>
                                <a href="{{ route('login') }}" class="mg-dropdown-item">
                                    <i class="fas fa-sign-in-alt"></i> Sign In
                                </a>
                                <a href="{{ route('register') }}" class="mg-dropdown-item">
                                    <i class="fas fa-user-plus"></i> Sign Up
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Mobile Actions (Right Side) -->
                <div class="mg-mobile-actions d-lg-none">
                    <button class="mg-mobile-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Bar (Below Header) -->
    <div class="mg-mobile-nav-bar d-lg-none">
        <div class="container">
            <div class="mg-mobile-nav-scroll justify-content-center">
                <a href="{{ route('game.category') }}" class="mg-mobile-nav-item {{ request()->routeIs('game.category') ? 'active' : '' }}">
                    <i class="fas fa-gamepad"></i> Games
                </a>
                <button class="mg-mobile-nav-item" onclick="toggleMobileHotDeals()" id="btnHotDeals">
                    <i class="fas fa-fire"></i> Hot Deals
                </button>
                @auth
                    <a href="{{ route('notifications.inbox') }}"
                       id="mobile-inbox-link"
                       class="mg-mobile-nav-item {{ request()->routeIs('notifications.inbox') ? 'active' : '' }}">
                        <i class="fas fa-inbox"></i> Inbox
                    </a>
                    <a href="{{ route('user.kpay.orders') }}" class="mg-mobile-nav-item {{ request()->routeIs('user.kpay.orders') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i> Orders
                    </a>
                @endauth
            </div>
        </div>
    </div>
    
    <!-- Mobile Hot Deals Dropdown -->
    <div class="mg-mobile-hot-deals d-lg-none" id="mobileHotDeals">
        <div class="container">
            <div class="mg-hot-deals-card">
                <a href="{{ route('mlproducts') }}" class="mg-hot-deal-item">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Mobile Legends</span>
                </a>
                <a href="{{ route('pubg') }}" class="mg-hot-deal-item">
                    <i class="fas fa-crosshairs"></i>
                    <span>PUBG Mobile</span>
                </a>
                <a href="{{ route('mcgg') }}" class="mg-hot-deal-item">
                    <i class="fas fa-chess"></i>
                    <span>Magic Chess</span>
                </a>
                <a href="{{ route('wwm') }}" class="mg-hot-deal-item">
                    <i class="fas fa-wind"></i>
                    <span>Where Winds Meet</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation (Desktop Only) -->
    <nav class="mg-navigation d-none d-lg-block">
        <div class="container">
            <ul class="mg-nav-list">
                <li class="mg-nav-item">
                    <a href="{{ route('game.category') }}" class="mg-nav-link {{ request()->routeIs('game.category') ? 'active' : '' }}">
                        <i class="fas fa-gamepad"></i> Games
                    </a>
                </li>
                @auth
                    <li class="mg-nav-item">
                        <a href="{{ route('notifications.inbox') }}"
                           id="desktop-inbox-link"
                           class="mg-nav-link {{ request()->routeIs('notifications.inbox') ? 'active' : '' }}">
                            <i class="fas fa-inbox"></i> Inbox
                        </a>
                    </li>
                    <li class="mg-nav-item">
                        <a href="{{ route('user.kpay.orders') }}" class="mg-nav-link {{ request()->routeIs('user.kpay.orders') ? 'active' : '' }}">
                            <i class="fas fa-receipt"></i> My Orders
                        </a>
                    </li>
                @endauth
                <li class="mg-nav-item mg-dropdown">
                    <a href="#" class="mg-nav-link">
                        <i class="fas fa-fire"></i> Hot Deals
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="mg-mega-menu">
                        <div class="mg-mega-content">
                            <div class="mg-mega-section">
                                <h4>🔥 Popular Now</h4>
                                <a href="{{ route('mlproducts') }}">Mobile Legends: Bang Bang</a>
                                <a href="{{ route('pubg') }}">PUBG Mobile (UC)</a>
                            </div>
                            <div class="mg-mega-section">
                                <h4>🆕 New Arrivals</h4>
                                <a href="{{ route('mcgg') }}">Magic Chess: GoGo</a>
                                <a href="{{ route('wwm') }}">Where Winds Meet</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="mg-nav-item mg-dropdown">
                    <a href="#" class="mg-nav-link">
                        <i class="fas fa-info-circle"></i> About
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="mg-dropdown-menu">
                        <a href="#" class="mg-dropdown-item">
                            <i class="fas fa-building"></i> About Us
                        </a>
                        <a href="{{ route('payment-methods') }}" class="mg-dropdown-item">
                            <i class="fas fa-credit-card"></i> Payment Methods
                        </a>
                        <a href="{{ route('contact') }}" class="mg-dropdown-item">
                            <i class="fas fa-envelope"></i> Contact Us
                        </a>
                        <a href="#" class="mg-dropdown-item">
                            <i class="fas fa-question-circle"></i> FAQ
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="mg-mobile-nav" id="mobileNav">
        <div class="mg-mobile-nav-header">
            <div class="mg-mobile-logo">
                <span>{{ config('app.name', 'App') }}</span>
            </div>
            <button class="mg-close-menu" id="closeMobileMenu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mg-mobile-nav-content">
            @auth
                <div class="mg-mobile-user-info">
                    <div class="mg-mobile-user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="mg-mobile-user-details">
                        <strong>{{ auth()->user()->name ?? auth()->user()->email }}</strong>
                        <small>
                            @if(auth()->user()->role === 'admin')
                                <span class="mg-role-badge">Administrator</span>
                            @elseif(!empty(auth()->user()->role))
                                <span class="mg-role-badge">{{ ucfirst(auth()->user()->role) }}</span>
                            @else
                                Member
                            @endif
                        </small>
                    </div>
                </div>

                <div class="mg-mobile-nav-divider"></div>

                <a href="{{ route('notifications.inbox') }}"
                   id="mobile-menu-inbox-link"
                   class="mg-mobile-nav-link">
                    <i class="fas fa-inbox"></i> Inbox
                </a>
                <a href="{{ route('user.kpay.orders') }}" class="mg-mobile-nav-link">
                    <i class="fas fa-receipt"></i> My Orders
                </a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="mg-mobile-nav-link mg-admin-link">
                        <i class="fas fa-crown"></i> Admin Panel
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mg-mobile-nav-link mg-logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mg-mobile-nav-link">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
                <a href="{{ route('register') }}" class="mg-mobile-nav-link">
                    <i class="fas fa-user-plus"></i> Sign Up
                </a>
            @endauth
        </div>
    </div>
</header>



@auth
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let notifInitialized = false;
        const seenIds = new Set();
        function showToast(n) {
            if (typeof Swal === 'undefined') return;
            let icon = 'info';
            if (n.type === 'success') icon = 'success';
            else if (n.type === 'error') icon = 'error';
            else if (n.type === 'warning') icon = 'warning';
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon,
                title: n.title || '',
                text: n.message || '',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        }
        function pollToasts() {
            if (!navigator.onLine) return;
            fetch('/notifications/unread', { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    const count = data.count || 0;

                    const links = [
                        document.getElementById('desktop-inbox-link'),
                        document.getElementById('mobile-inbox-link'),
                        document.getElementById('mobile-menu-inbox-link')
                    ];

                    links.forEach(link => {
                        if (!link) return;
                        if (count > 0) {
                            link.classList.add('mg-inbox-shake');
                            link.classList.add('mg-inbox-alert');
                        } else {
                            link.classList.remove('mg-inbox-shake');
                            link.classList.remove('mg-inbox-alert');
                        }
                    });

                    const arr = Array.isArray(data.notifications) ? data.notifications : [];
                    if (!notifInitialized) {
                        arr.forEach(n => seenIds.add(n.id));
                        notifInitialized = true;
                        return;
                    }
                    const fresh = arr.filter(n => !seenIds.has(n.id));
                    fresh.forEach(n => {
                        seenIds.add(n.id);
                        showToast(n);
                    });
                })
                .catch(() => {});
        }
        pollToasts();
        setInterval(pollToasts, 2000);
        @if(auth()->user()->role === 'admin')
        let lastPendingOrderCount = null;
        function updateAdminOrderBadge(count) {
            const badge = document.getElementById('admin-orders-count-badge');
            if (!badge) return;
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
        function pollAdminOrders() {
            if (!navigator.onLine) return;
            fetch("{{ route('admin.kpay.orders.fetch') }}", { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    const count = data.pending_count || 0;
                    updateAdminOrderBadge(count);
                    if (lastPendingOrderCount !== null && count > lastPendingOrderCount) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'info',
                                title: 'New orders received',
                                text: count + ' pending orders waiting',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true
                            });
                        }
                    }
                    lastPendingOrderCount = count;
                })
                .catch(() => {});
        }
        pollAdminOrders();
        setInterval(pollAdminOrders, 5000);
        @endif
    });
</script>
@endauth

<script>
    function toggleMobileHotDeals() {
        const el = document.getElementById('mobileHotDeals');
        const btn = document.getElementById('btnHotDeals');
        
        if (el.classList.contains('active')) {
            el.classList.remove('active');
            btn.classList.remove('active');
        } else {
            el.classList.add('active');
            btn.classList.add('active');
        }
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const hotDeals = document.getElementById('mobileHotDeals');
        const btnHotDeals = document.getElementById('btnHotDeals');
        
        if (hotDeals && hotDeals.classList.contains('active')) {
            if (!hotDeals.contains(event.target) && !btnHotDeals.contains(event.target)) {
                hotDeals.classList.remove('active');
                btnHotDeals.classList.remove('active');
            }
        }
    });
</script>



