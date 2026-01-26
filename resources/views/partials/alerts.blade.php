<div class="ml-alert-container" style="z-index: 10000;">
    @if (session('success'))
        <div class="ml-alert-card success">
            <div class="ml-alert-icon"><i class="fas fa-check-circle"></i></div>
            <div class="ml-alert-content">
                <div class="ml-alert-title">Success</div>
                <div class="ml-alert-message">{{ session('success') }}</div>
            </div>
            <button type="button" class="ml-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="ml-alert-card error">
            <div class="ml-alert-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="ml-alert-content">
                <div class="ml-alert-title">Error</div>
                <div class="ml-alert-message">{{ session('error') }}</div>
            </div>
            <button type="button" class="ml-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('warning'))
        <div class="ml-alert-card warning">
            <div class="ml-alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="ml-alert-content">
                <div class="ml-alert-title">Warning</div>
                <div class="ml-alert-message">{{ session('warning') }}</div>
            </div>
            <button type="button" class="ml-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('info'))
        <div class="ml-alert-card info">
            <div class="ml-alert-icon"><i class="fas fa-info-circle"></i></div>
            <div class="ml-alert-content">
                <div class="ml-alert-title">Info</div>
                <div class="ml-alert-message">{{ session('info') }}</div>
            </div>
            <button type="button" class="ml-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="ml-alert-card error">
            <div class="ml-alert-icon"><i class="fas fa-times-circle"></i></div>
            <div class="ml-alert-content">
                <div class="ml-alert-title">Please Check</div>
                <div class="ml-alert-message">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="ml-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif
</div>

<script>
    // Auto dismiss after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.ml-alert-card');
        alerts.forEach(alert => {
            // Only auto-dismiss success and info
            if (alert.classList.contains('success') || alert.classList.contains('info')) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(100%)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal === 'undefined') return;
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif
        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: @json(session('error')),
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif
        @if (session('warning'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: @json(session('warning')),
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif
        @if (session('info'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: @json(session('info')),
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif
    });
</script>

<!-- Mobile Alert (SweetAlert2) -->
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    (function() {
        if (typeof window.Swal === 'undefined') {
            var s = document.createElement('script');
            s.src = 'https://unpkg.com/sweetalert2@11/dist/sweetalert2.all.min.js';
            s.defer = true;
            s.setAttribute('crossorigin','anonymous');
            s.setAttribute('referrerpolicy','no-referrer');
            document.head.appendChild(s);
        }
    })();
 </script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile/user alert: show unread notifications (success/error/info/warning)
        try {
            fetch('/notifications/unread', { credentials: 'same-origin' })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => {
                    if (!data || !data.notifications || !Array.isArray(data.notifications) || data.notifications.length === 0) {
                        return;
                    }

                    const n = data.notifications[0];
                    const type = (n.type || 'info').toLowerCase();
                    const icon = type === 'success' ? 'success'
                        : type === 'error' ? 'error'
                        : type === 'warning' ? 'warning'
                        : 'info';

                    Swal.fire({
                        title: n.title || (type.charAt(0).toUpperCase() + type.slice(1)),
                        text: n.message || '',
                        icon,
                        confirmButtonText: 'OK',
                    }).then(() => {
                        const tokenEl = document.querySelector('meta[name=\"csrf-token\"]');
                        const token = tokenEl ? tokenEl.getAttribute('content') : '';
                        if (n.read_url && token) {
                            fetch(n.read_url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                credentials: 'same-origin'
                            }).catch(() => {});
                        }
                    });
                })
                .catch(() => {});
        } catch (e) {}
    });
</script>
