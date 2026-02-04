@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Back Button -->
            <div class="mb-3 mb-lg-4">
                <a href="{{ url('/') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Go Back
                </a>
            </div>

            <!-- Header -->
            <div class="text-center mb-4 mb-lg-5">
                <h1 class="fw-bold about-title mb-2">About Us</h1>
                <p class="about-subtitle">
                    Learn more about {{ config('app.name', 'Skins Collector') }}
                </p>
            </div>

            <!-- Card -->
            <div class="card about-card border-0 shadow-sm">
                <div class="card-body">

                    <!-- Who We Are -->
                    <div class="about-section">
                        <h4 class="section-title">
                            <i class="fas fa-users text-primary me-2"></i>
                            Who We Are
                        </h4>
                        <p class="section-text">
                            {{ config('app.name', 'Skins Collector') }}သည် မြန်မာနိုင်ငံရှိ ဂိမ်းဝါသနာရှင်များအတွက် အထူးရည်ရွယ်ပြီး 
                            ပြည်တွင်း Engineer၊ Programmer များနှင့် Software Developer များကိုယ်တိုင် ဖန်တီးထားသည့် Platform တစ်ခု ဖြစ်ပါသည်။
                            Game Top-up နှင့် Digital Products များကို အလုံခြုံဆုံး၊ အမြန်ဆုံး ဝယ်ယူနိုင်ပါသည်။
                        </p>
                    </div>

                    <!-- Mission -->
                    <div class="about-section">
                        <h4 class="section-title">
                            <i class="fas fa-bullseye text-primary me-2"></i>
                            Our Mission
                        </h4>
                        <p class="section-text">
                            မြန်မာ Gamer များအတွက် အချိန်ကုန်၊ လူပင်ပန်းမှုမရှိဘဲ စိတ်ချလက်ချ 
                            ဂိမ်းများကို ခံစားနိုင်စေရန်ဖြစ်ပါသည်။
                            စက္ကန့်ပိုင်းအတွင်း ဝန်ဆောင်မှုရရှိစေမည့် 
                            Gamer-Centric Platform တစ်ခုအဖြစ် ရပ်တည်သွားရန် ဖြစ်ပါသည်။
                        </p>
                    </div>

                    <!-- Why Choose Us -->
                    <div class="about-section mb-0">
                        <h4 class="section-title">
                            <i class="fas fa-star text-primary me-2"></i>
                            Why Choose Us?
                        </h4>

                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle"></i> မှာပြီးတာနဲ့ ချက်ချင်းရောက်</li>
                            <li><i class="fas fa-check-circle"></i> 100% စိတ်ချရသော Payment စနစ်</li>
                            <li><i class="fas fa-check-circle"></i> 6:00 AM – 10:00 PM ဝန်ဆောင်မှု</li>
                            <li><i class="fas fa-check-circle"></i> စျေးနှုန်းနှင့် ဝန်ဆောင်မှုမျှတ</li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

<style>
/* Back Button */
.btn-back {
    border: 1.8px solid #e9ecef;
    color: #6c757d;
    padding: 10px 20px;
    border-radius: 50px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    transition: 0.3s ease;
}
.btn-back:hover {
    border-color: #0d6efd;
    color: #0d6efd;
    transform: translateX(-4px);
}

/* Header */
.about-title {
    font-size: clamp(1.8rem, 5vw, 2.5rem);
}
.about-subtitle {
    color: #6c757d;
    font-size: 0.95rem;
}

/* Card */
.about-card {
    border-radius: 18px;
}
.about-card .card-body {
    padding: 1.5rem;
}

/* Sections */
.about-section {
    margin-bottom: 2rem;
}
.section-title {
    font-weight: 700;
    margin-bottom: 0.75rem;
    font-size: 1.15rem;
}
.section-text {
    color: #6c757d;
    line-height: 1.7;
    font-size: 0.95rem;
}

/* Features */
.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.feature-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 12px;
    background: #f8f9fa;
    margin-bottom: 10px;
    font-size: 0.95rem;
}
.feature-list i {
    color: #0d6efd;
}

/* Mobile tweaks */
@media (max-width: 576px) {
    .about-card .card-body {
        padding: 1.25rem;
    }
    .btn-back {
        width: 100%;
        justify-content: center;
    }
}
</style>
