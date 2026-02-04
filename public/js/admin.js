
document.addEventListener('DOMContentLoaded', function() {
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar && overlay) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    };

    // Global Pending Order Poller
    const countBadge = document.getElementById('global-pending-count');
    
    if (countBadge) {
        fetchPendingCount();
        setInterval(fetchPendingCount, 15000); // Poll every 15 seconds
    }

    function fetchPendingCount() {
        fetch('/admin/confirm-orders/count')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.count > 0) {
                    countBadge.textContent = data.count > 99 ? '99+' : data.count;
                    countBadge.style.display = 'inline-block';
                } else {
                    countBadge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error fetching pending count:', error));
    }
});
