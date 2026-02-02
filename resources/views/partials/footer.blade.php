<footer class="mg-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <a href="{{ url('/') }}" class="mg-footer-brand">
                    <img src="{{ asset('adminimages/logo/skincollector.jpg') }}" alt="{{ config('app.name', 'Skins Collector') }}" loading="lazy" decoding="async">
                </a>
                <span>{{ config('app.name', 'Skins Collector') }}</span>
                <p class="mg-footer-desc">
                    Game top-ups and digital products with instant delivery and secure payments.
                </p>
            </div>

            <!-- Support -->
            <div class="col-lg-3 col-6 mb-4 ms-auto">
                <h5 class="mg-footer-heading">Support</h5>
                <ul class="mg-footer-links">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
        </div>

        <div class="mg-footer-bottom">
            <div class="row align-items-center">
                <div class="col-12 text-start">
                    <p class="mb-0">© {{ date('Y') }} {{ config('app.name', 'Skins Collector') }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
