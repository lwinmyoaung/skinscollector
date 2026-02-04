@extends('layouts.app')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 text-center">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4 text-success d-flex justify-content-center">
                        @php
                            $orderVideoPath = \Illuminate\Support\Facades\Cache::remember('payment.success.video', 3600, function () {
                                $files = \Illuminate\Support\Facades\Storage::disk('adminimages')->files('orderconfirmimage');
                                foreach ($files as $file) {
                                    if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'mp4') {
                                        return $file;
                                    }
                                }
                                return null;
                            });
                        @endphp

                        @if($orderVideoPath)
                            <video autoplay muted loop playsinline style="max-width: 150px; height: auto;">
                                <source src="{{ asset('adminimages/'.$orderVideoPath) }}" type="video/mp4">
                                <i class="fas fa-check-circle fa-5x"></i>
                            </video>
                        @else
                            <i class="fas fa-check-circle fa-5x"></i>
                        @endif
                    </div>
                    
                    
                    <div class="text-muted mb-4">
                        <p class="fw-bold text-dark mb-3" style="font-size: 1.1rem;">
                            ဝယ်ယူအားပေးမှုအတွက် အထူးကျေးဇူးတင်ပါသည်။
                        </p>
                        <p class="mb-2 small text-secondary">
                            ၅ မိနစ်အတွင်း လူကြီးမင်းဝယ်ယူထားသော Item ရောက်ရှိပါမည်။
                            Admin ဘက်မှ အချက်အလက်များကို စစ်ဆေးနေပြီး Approved ဖြစ်ပါက Notification ပေးပို့ပါမည်။
                        </p>
                        <p class="mb-0 small text-danger fw-bold">
                            <i class="fas fa-clock me-1"></i>
                            Game Item များကို မနက်(၆)နာရီမှ ည(၁၀)နာရီအတွင်းသာ ဆောင်ရွက်ပေးပါသည်။
                        </p>
                    </div>
                    
                    <div class="d-grid gap-2 col-12 col-md-10 mx-auto">
                        <a href="{{ route('game.category') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i> ပင်မစာမျက်နှာသို့
                        </a>
                        <a href="{{ route('user.kpay.orders') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-history me-2"></i> Order မှတ်တမ်း
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
