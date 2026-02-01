<style>
    :root {
        --primary-color: var(--primary);
        --primary-hover: var(--primary-dark);
        --bg-color: #f4f6f9; /* Soft Light Gray Background */
        --card-bg: #ffffff;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --border-color: #d3d6d6ff;
        --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        --input-bg: #f8f9fa;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-color);
        background-attachment: scroll;
        color: var(--text-dark);
        min-height: 100vh;
    }

    /* Page Wrapper */
    .ml-page-wrapper {
        padding-top: 1rem;
        padding-bottom: 120px;
    }

    /* Header Mobile */
    .ml-header-mobile {
        margin-bottom: 1rem;
    }
    
    .ml-header-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .ml-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .ml-mobile-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .ml-mobile-subtitle {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* Standard Card */
    .ml-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: none;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    
    .ml-card-header {
        background: #fff;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
    }

    .ml-step-circle {
        width: 28px;
        height: 28px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 0.75rem;
        font-size: 0.8rem;
        box-shadow: none;
    }

    .ml-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .ml-card-body {
        padding: 0.75rem 1rem;
    }

    /* Category Headers */
    .category-header {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--border-color);
        margin-top: 0.5rem;
    }
    
    .category-header i {
        color: var(--primary-color);
        margin-right: 10px;
        font-size: 1.1rem;
    }

    /* Product Buttons - Standard Style */
    .product-btn {
        width: 100%;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        position: relative;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        text-align: left;
        gap: 0.4rem;
    }

    .product-btn:hover {
        border-color: var(--primary-color);
        background-color: #fcfcfc;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .btn-check:checked + .product-btn {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.2);
        transform: translateY(-1px);
    }

    .product-name {
        font-weight: 600;
        font-size: 0.85rem;
        line-height: 1.2;
        color: var(--text-dark);
        margin-bottom: 0;
        white-space: nowrap;
    }

    .product-price {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
        white-space: nowrap;
    }

    .btn-check:checked + .product-btn .product-name,
    .btn-check:checked + .product-btn .product-price {
        color: white;
        font-weight: 600;
    }

    /* Check Circle Animation */
    .check-circle {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 18px;
        height: 18px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 10px;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.2s ease;
    }

    .btn-check:checked + .product-btn .check-circle {
        opacity: 1;
        transform: scale(1);
        background: white;
    }

    .product-icon {
        width: 40px;
        height: 40px;
        object-fit: contain;
        margin-bottom: 0.5rem;
    }
    
    /* Region Selector Standard */
    .ml-region-card {
        border: 1px solid var(--border-color);
        background: #fff;
        border-radius: 12px;
        padding: 0.5rem 0.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s;
        height: 100%;
        min-height: 70px;
        justify-content: center;
    }
    
    .ml-region-input:checked + .ml-region-card {
        border-color: var(--primary-color);
        background-color: rgba(var(--primary-rgb), 0.08);
        color: var(--primary-color);
        box-shadow: 0 0 0 1px var(--primary-color);
    }
    
    .ml-region-flag-img {
        width: 28px;
        height: auto;
        margin-bottom: 4px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .ml-region-name {
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.2;
    }
    
    .ml-check-icon {
        display: none; /* Hide old icon */
    }

    /* Forms */
    .form-control {
        background-color: var(--input-bg);
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
    }
    
    .form-control:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: none;
    }
    
    .form-floating > label {
        color: var(--text-muted);
    }
    
    .ml-helper-text {
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .ml-card-body {
            padding: 1rem;
        }
        .product-btn {
            padding: 0.75rem 0.5rem;
        }
        .ml-mobile-title {
            font-size: 1.25rem;
        }
    }
    /* Desktop Sidebar */
    .product-info-card {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .ml-product-image-wrapper {
        position: relative;
        padding-top: 60%;
        overflow: hidden;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    
    .ml-product-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ml-product-details {
        padding: 25px;
    }
    
    .ml-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .ml-badge-instant {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(46, 204, 113, 0.95);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        z-index: 10;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .ml-details-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--text-dark);
        position: relative;
        padding-bottom: 10px;
    }

    .ml-details-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 2px;
    }
    
    .step-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .step-num {
        background: rgba(var(--primary-rgb), 0.1);
        color: var(--primary-color);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
        margin-right: 12px;
    }
    
    .step-text {
        font-size: 0.95rem;
        color: #555;
    }

    @media (max-width: 991px) {
    .mobile-sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 8px 12px;
        box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
        z-index: 9999;
        border-top: 1px solid #eee;
        margin-top: 0 !important;
        animation: slideUp 0.3s ease-out;
    }
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
</style>