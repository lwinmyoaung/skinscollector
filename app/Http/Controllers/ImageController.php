<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pms = PaymentMethod::all();

        return view('admin.paymentshow', compact('pms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.addpaymentmethod');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pm = new PaymentMethod;
        $pm->name = $request->name;
        $pm->phone_number = $request->phone_number;

        // Option 1: Fast Storage Flow
        $path = request()->image->store('payment_methods', 'public');

        $pm->image = $path;
        $pm->save();

        return redirect('admin/paymentmethod')->with('message', 'New Payment successfully added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentmethod)
    {
        return view('admin.editpaymentmethod', compact('paymentmethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentmethod)
    {

        request()->validate([
            'name' => 'required',
            'image' => 'required',
        ]);
        $paymentmethod->name = $request->name;
        $paymentmethod->phone_number = $request->phone_number;

        if ($request->image) {
            // Option 1: Fast Storage Flow
            $path = request()->image->store('payment_methods', 'adminimages');
            $paymentmethod->image = $path;
        }

        $paymentmethod->save();

        return redirect('admin/paymentmethod')->with('message', 'New Paymentmethod updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentmethod)
    {
        $paymentmethod->delete();

        return redirect('admin/paymentmethod')->with('message', 'Paymentmethod removed successfully.');
    }
}
