<?php

namespace App\Http\Controllers;

use App\Models\GameImage;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // View composer already shares gameImages, but the original closure passed it too.
        // We'll keep passing it to be safe, but ideally we should rely on one mechanism.
        // For now, let's replicate the closure behavior exactly.
        $gameImages = GameImage::all()->keyBy('game_code');
        return view('games', compact('gameImages'));
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
