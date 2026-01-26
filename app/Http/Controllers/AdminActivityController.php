<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\TopUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function getPendingOrdersCount()
    {
        $count = TopUp::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }

    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        $logs = Notification::whereDate('created_at', $date)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.activity_logs', compact('logs', 'date'));
    }

    public function deleteOldLogs(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $days = $request->input('days');
        $date = \Carbon\Carbon::now()->subDays($days)->format('Y-m-d');

        $count = Notification::whereDate('created_at', '<', $date)->delete();

        return redirect()->back()->with('success', "Deleted {$count} logs older than {$days} days.");
    }
}
