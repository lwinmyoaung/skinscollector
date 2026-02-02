<?php

namespace App\Http\Controllers;

use App\Models\GameImage;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Cache the directory scanning for slides (1 hour)
        $advertiseSlides = \Illuminate\Support\Facades\Cache::remember('home.slides', 3600, function () {
            $files = \Illuminate\Support\Facades\Storage::disk('public')->files('ads/slides');
            $slides = array_filter($files, function($file) {
                return preg_match('/\.(jpg|jpeg|png|webp)$/i', $file);
            });
            sort($slides);
            return $slides;
        });

        // gameImages is provided by View Composer (AppServiceProvider)
        return view('games', compact('advertiseSlides'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function about()
    {
        return view('about');
    }

    public function paymentMethods()
    {
        $paymentMethods = PaymentMethod::all();
        return view('payment-methods', compact('paymentMethods'));
    }
}
