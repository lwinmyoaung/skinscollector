<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\TopUp;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::all();

        return view('user/topup', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1️⃣ Validate
        $request->validate([
            'topupamount' => 'required|numeric|min:1',
            'payment_method' => 'required',
            'transaction_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // 2️⃣ Upload transaction image
        $imagePath = $request->file('transaction_image')
            ->store('topups', 'public');

        // 3️⃣ Save topup record (PENDING)
        TopUp::create([
            'user_id' => Auth::id(),
            'amount' => $request->topupamount,
            'payment_method' => $request->payment_method,
            'transaction_image' => $imagePath,
            'status' => 'pending', // pending | approved | rejected
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Top Up Submitted',
            'message' => 'Your top up of '.number_format($request->topupamount).' MMK has been submitted and is pending approval.',
            'type' => 'info',
            'is_read' => false,
        ]);

        // 4️⃣ Redirect
        if ($request->has('redirect') && $request->redirect) {
            return redirect($request->redirect)->with('success', 'Top up request submitted. Please wait for approval.');
        }

        return redirect()->route('game.category')->with('success', 'Top up request submitted. Please wait for approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
