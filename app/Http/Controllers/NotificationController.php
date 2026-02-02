<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $notifications = $query->paginate(20);

        return view('user.inbox', compact('notifications'));
    }

    public function getUnread()
    {
        // Optimized: Only count total, but fetch limited notifications
        $query = Notification::where('user_id', Auth::id())
            ->where('is_read', false);
            
        $count = $query->count();
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->limit(10) // Limit to latest 10
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'created_at_human' => $notification->created_at->diffForHumans(),
                    'read_url' => route('notifications.read', $notification->id),
                ];
            });

        return response()->json([
            'count' => $count,
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return back();
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back();
    }
}
