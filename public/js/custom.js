
document.addEventListener('DOMContentLoaded', function() {
    // Game Products Logic

    // Quick View Functionality
    const quickViewBtns = document.querySelectorAll('.quick-view');
    if (quickViewBtns.length > 0) {
        quickViewBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const card = this.closest('.game-product-card');
                const title = card.querySelector('.product-title a').textContent;
                const priceElement = card.querySelector('.current-price');
                const price = priceElement ? priceElement.textContent : 'Price N/A';
                
                // Create modal for quick view
                const modalHTML = `
                    <div class="modal fade" id="quickViewModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">${title}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Quick preview for <strong>${title}</strong> at ${price}</p>
                                    <p>This feature would show product details, images, and options.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">View Full Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add modal to body and show it
                document.body.insertAdjacentHTML('beforeend', modalHTML);
                if (typeof bootstrap !== 'undefined') {
                    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
                    modal.show();
                    
                    // Remove modal after hiding
                    document.getElementById('quickViewModal').addEventListener('hidden.bs.modal', function () {
                        this.remove();
                    });
                }
            });
        });
    }

    // Add to Cart Functionality
    const cartBtns = document.querySelectorAll('.btn-cart:not(:disabled)');
    if (cartBtns.length > 0) {
        cartBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const originalHTML = this.innerHTML;
                const card = this.closest('.game-product-card');
                const title = card.querySelector('.product-title a').textContent;
                
                // Show loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                // Simulate API call
                setTimeout(() => {
                    // Success state
                    this.innerHTML = '<i class="fas fa-check"></i> Added';
                    this.classList.add('btn-success');
                    
                    // Show notification
                    showNotification(`"${title}" added to cart!`);
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                        this.classList.remove('btn-success');
                        this.disabled = false;
                    }, 2000);
                }, 800);
            });
        });
    }

    // Details Button Functionality
    const detailsBtns = document.querySelectorAll('.btn-details');
    if (detailsBtns.length > 0) {
        detailsBtns.forEach(btn => {
            // Save original HTML
            if (!btn.getAttribute('data-original-html')) {
                btn.setAttribute('data-original-html', btn.innerHTML);
            }

            btn.addEventListener('click', function() {
                // Show loading on button
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            });
        });
    }

    // Reset button state when page is shown (fixes back button issue)
    window.addEventListener('pageshow', function() {
        const detailsBtns = document.querySelectorAll('.btn-details');
        detailsBtns.forEach(btn => {
            const originalHtml = btn.getAttribute('data-original-html');
            if (originalHtml) {
                btn.innerHTML = originalHtml;
            }
        });
    });

    // Show Notification Function
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        `;
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    if (typeof bootstrap !== 'undefined') {
        const modals = document.querySelectorAll('.modal');
        function normalizeModalAria(el) {
            el.removeAttribute('aria-hidden');
            el.setAttribute('aria-modal', 'true');
            if (!el.hasAttribute('role')) {
                el.setAttribute('role', 'dialog');
            }
        }
        modals.forEach(m => {
            m.addEventListener('shown.bs.modal', function() {
                normalizeModalAria(this);
            });
            m.addEventListener('hidden.bs.modal', function() {
                this.setAttribute('aria-hidden', 'true');
                this.removeAttribute('aria-modal');
            });
            if ((m.classList.contains('show') || m.style.display === 'block')) {
                normalizeModalAria(m);
            }
        });
    }
});

/* =========================================
   HEADER FUNCTIONALITY
   ========================================= */
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileToggle = document.querySelector('.mg-mobile-toggle');
    const mobileNav = document.querySelector('.mg-mobile-nav');
    const closeMenu = document.querySelector('.mg-close-menu');
    
    // Only proceed if mobile nav elements exist
    if (mobileNav) {
        const overlay = document.createElement('div');
        
        overlay.className = 'mg-mobile-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        `;
        document.body.appendChild(overlay);

        function toggleMenu() {
            mobileNav.classList.toggle('active');
            if (mobileNav.classList.contains('active')) {
                overlay.style.opacity = '1';
                overlay.style.visibility = 'visible';
                document.body.style.overflow = 'hidden';
            } else {
                overlay.style.opacity = '0';
                overlay.style.visibility = 'hidden';
                document.body.style.overflow = '';
            }
        }

        if (mobileToggle) {
            mobileToggle.addEventListener('click', toggleMenu);
        }

        if (closeMenu) {
            closeMenu.addEventListener('click', toggleMenu);
        }

        overlay.addEventListener('click', toggleMenu);
    }

    // Mobile Dropdown Toggle
    const dropdownToggles = document.querySelectorAll('.mg-toggle-dropdown');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const content = this.nextElementSibling;
            const icon = this.querySelector('.mg-dropdown-icon');
            
            if (content && icon) {
                content.classList.toggle('show');
                icon.classList.toggle('rotated');
            }
        });
    });

    // Smart Sticky Header (Hide on Scroll Down, Show on Scroll Up)
    const header = document.querySelector('.mg-header');
    let lastScrollTop = 0;
    
    if (header) {
        const syncBodyPaddingToHeader = () => {
            if (document.body.classList.contains('admin-body')) {
                return;
            }

            const headerHeight = header.offsetHeight || 0;
            document.body.style.paddingTop = `${headerHeight}px`;
        };

        syncBodyPaddingToHeader();
        window.addEventListener('resize', syncBodyPaddingToHeader);

        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scroll Down > Hide
                header.classList.add('header-hidden');
            } else {
                // Scroll Up > Show
                header.classList.remove('header-hidden');
            }
            
            // Add shadow when scrolled
            if (scrollTop > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
        });
    }

    // Dropdown Hover (Desktop)
    const dropdowns = document.querySelectorAll('.mg-user-dropdown, .mg-nav-item.mg-dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('mouseenter', function() {
            const menu = this.querySelector('.mg-dropdown-menu, .mg-mega-menu');
            if (menu) {
                menu.style.opacity = '1';
                menu.style.visibility = 'visible';
                menu.style.transform = 'translateY(0)';
            }
        });
        
        dropdown.addEventListener('mouseleave', function() {
            const menu = this.querySelector('.mg-dropdown-menu, .mg-mega-menu');
            if (menu) {
                menu.style.opacity = '0';
                menu.style.visibility = 'hidden';
                menu.style.transform = 'translateY(20px)';
            }
        });
    });

    // Active state for nav links
    const navLinks = document.querySelectorAll('.mg-nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }
            // Only toggle if not navigating away (optional, but good for SPA feel or # links)
            if (this.getAttribute('href').startsWith('#')) {
                 navLinks.forEach(l => l.classList.remove('active'));
                 this.classList.add('active');
            }
        });
    });
});

// Mobile Search Toggle (Global)
window.toggleMobileSearch = function() {
    const overlay = document.getElementById('mobileSearchOverlay');
    if (overlay) {
        overlay.classList.toggle('active');
        if (overlay.classList.contains('active')) {
            const input = overlay.querySelector('input');
            if (input) setTimeout(() => input.focus(), 100);
        }
    }
};
