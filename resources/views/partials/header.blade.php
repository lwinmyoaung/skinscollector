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
                                <a href="{{ route('notifications.inbox') }}" class="mg-dropdown-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-inbox"></i> Inbox</span>
                                    <span class="badge rounded-pill bg-danger inbox-badge" style="display: none;">0</span>
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
                                
                                <div class="px-3 py-2">
                                    <div class="alert alert-primary mb-0 d-flex align-items-start p-2" style="font-size: 0.8rem; border-radius: 6px;">
                                        <i class="fas fa-info-circle me-2 mt-1"></i>
                                        <span>သာမန် user များအကောင့်ဖောက်ရန်မလိုအပ်ပါ</span>
                                    </div>
                                </div>

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
                <div class="mg-mobile-actions d-lg-none d-flex align-items-center">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <div class="position-relative me-3">
                                <a href="{{ route('admin.dashboard') }}" class="mg-mobile-icon-btn" id="mobile-orders-btn">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="mobile-admin-orders-badge" style="display: none; font-size: 0.6rem;">0</span>
                                </a>
                            </div>
                        @endif
                    @endauth
                    
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
                       class="mg-mobile-nav-item {{ request()->routeIs('notifications.inbox') ? 'active' : '' }} position-relative">
                        <i class="fas fa-inbox"></i> Inbox
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger inbox-badge" style="display: none; font-size: 0.6rem; margin-left: -10px; margin-top: 5px;">0</span>
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
                           class="mg-nav-link {{ request()->routeIs('notifications.inbox') ? 'active' : '' }} position-relative">
                            <i class="fas fa-inbox"></i> Inbox
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger inbox-badge" style="display: none; font-size: 0.6rem;">0</span>
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
                   class="mg-mobile-nav-link d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-inbox"></i> Inbox</span>
                    <span class="badge rounded-pill bg-danger inbox-badge" style="display: none;">0</span>
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
                <div class="px-3 py-2">
                    <div class="alert alert-primary mb-0 d-flex align-items-start p-2" style="font-size: 0.85rem; border-radius: 8px;">
                        <i class="fas fa-info-circle me-2 mt-1"></i>
                        <span>သာမန် user များအကောင့်ဖောက်ရန်မလိုအပ်ပါ</span>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="mg-mobile-nav-link">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
                <a href="{{ route('register') }}" class="mg-mobile-nav-link">
                    <i class="fas fa-user-plus"></i> Sign Up
                </a>
            @endauth
        </div>
    </div>
    <script>
        function toggleMobileAdminOrders() {
            const list = document.getElementById('mobile-admin-orders-list');
            const btn = document.getElementById('mobile-orders-btn');
            
            if (list.style.display === 'block') {
                list.style.display = 'none';
                btn.classList.remove('active');
            } else {
                list.style.display = 'block';
                btn.classList.add('active');
            }
        }
        
        // Close mobile dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const list = document.getElementById('mobile-admin-orders-list');
            const btn = document.getElementById('mobile-orders-btn');
            if (list && list.style.display === 'block' && !list.contains(event.target) && !btn.contains(event.target)) {
                list.style.display = 'none';
                btn.classList.remove('active');
            }
        });

        @auth
        // Poll for unread notifications
        function checkUnreadNotifications() {
            fetch("{{ route('notifications.unread') }}")
                .then(response => response.json())
                .then(data => {
                    const count = data.count;
                    const badges = document.querySelectorAll('.inbox-badge');
                    badges.forEach(badge => {
                        if (count > 0) {
                            badge.innerText = count > 99 ? '99+' : count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    });
                })
                .catch(error => console.error('Error checking notifications:', error));
        }

        // Check immediately and then every 10 seconds
        checkUnreadNotifications();
        setInterval(checkUnreadNotifications, 10000);
        @endauth
    </script>
    <style>
        .mg-orders-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 300px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: none;
            z-index: 1000;
            overflow: hidden;
            border: 1px solid #eee;
            margin-top: 10px;
        }

        #admin-orders-link {
            position: relative;
        }

        #admin-orders-link:hover .mg-orders-dropdown {
            display: block;
        }

        .mg-orders-header {
            padding: 10px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .mg-orders-header a {
            font-size: 0.8rem;
            color: #0d6efd;
            text-decoration: none;
        }

        .mg-orders-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .mg-order-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f1f1f1;
            transition: background 0.2s;
        }

        .mg-order-item:last-child {
            border-bottom: none;
        }

        .mg-order-item:hover {
            background: #f8f9fa;
        }

        .mg-order-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .mg-order-info {
            flex: 1;
            overflow: hidden;
        }

        .mg-order-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mg-order-meta {
            font-size: 0.75rem;
            color: #888;
        }

        /* Mobile specific styles */
        .mg-mobile-icon-btn {
             border: none;
             font-size: 1.2rem;
             color: #ffffffff;
             padding: 5px 10px;
             position: relative;
             display: inline-block;
             border-radius: 4px;
             text-decoration: none;
         }
        
        .mg-mobile-icon-btn.active {
            color: #0d6efd;
        }

        .mg-mobile-orders-dropdown {
            width: 280px;
            right: -50px; /* Shift slightly left to align better on mobile */
        }
        
        @media (max-width: 576px) {
            .mg-mobile-orders-dropdown {
                width: 260px;
                right: -60px;
            }
        }
    </style>
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
            const mobileBadge = document.getElementById('mobile-admin-orders-badge');
            
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
            
            if (mobileBadge) {
                if (count > 0) {
                    mobileBadge.textContent = count;
                    mobileBadge.style.display = 'inline-block';
                } else {
                    mobileBadge.style.display = 'none';
                }
            }
        }
        function pollAdminOrders() {
            if (!navigator.onLine) return;
            fetch("{{ route('admin.confirm.orders.fetch') }}", { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    const count = data.pending_count || 0;
                    updateAdminOrderBadge(count);
                    
                    // Update dropdown list if provided or handle manually
                    const listContainer = document.getElementById('admin-orders-list');
                    
                    if (data.html) {
                        // Extract rows from the table HTML for the dropdown preview
                        // This is a quick way to reuse the existing view without new controller logic
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data.html, 'text/html');
                        const rows = doc.querySelectorAll('tr');
                        
                        let html = '';
                        const adminOrdersUrl = "{{ route('admin.dashboard') }}";
                        
                        if (rows.length === 0 || count === 0) {
                             html = '<div class="text-center py-3 text-muted"><small>No pending orders</small></div>';
                        } else {
                            // Take first 5 rows
                            let limit = 0;
                            rows.forEach(row => {
                                if (limit >= 5) return;
                                const cells = row.querySelectorAll('td');
                                if (cells.length > 2) {
                                    // Extract basic info: ID/Game and Status/Time
                                    const info = cells[1] ? cells[1].innerText.trim() : 'Order';
                                    const meta = cells[4] ? cells[4].innerText.trim() : 'Pending';
                                    
                                    html += `
                                        <a href="${adminOrdersUrl}" class="mg-order-item text-decoration-none d-block">
                                            <div class="d-flex align-items-center">
                                                <div class="mg-order-icon bg-light text-primary me-2">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </div>
                                                <div class="mg-order-info">
                                                    <div class="mg-order-title text-dark">${info.substring(0, 25)}...</div>
                                                    <div class="mg-order-meta text-muted">${meta}</div>
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                    limit++;
                                }
                            });
                        }
                        
                        if (listContainer) listContainer.innerHTML = html;
                    }

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


