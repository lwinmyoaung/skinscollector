<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $isAllTime = $request->has('all_time') && $request->all_time == '1';

        $query = Order::query();

        if (!$isAllTime) {
            $query->whereDate('created_at', $date);
        }

        $orders = $query->latest()->get();

        $totalSales = $orders->sum('selling_price');
        $totalCost = $orders->sum('cost_price');
        $totalProfit = $orders->sum('profit');

        return view('admin.bank.index', compact('orders', 'totalSales', 'totalCost', 'totalProfit', 'date', 'isAllTime'));
    }

    public function deleteOldOrders(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $days = $request->input('days');
        $date = Carbon::now()->subDays($days)->format('Y-m-d');

        $count = Order::whereDate('created_at', '<', $date)->delete();

        return redirect()->back()->with('success', "Deleted {$count} orders older than {$days} days.");
    }
}
