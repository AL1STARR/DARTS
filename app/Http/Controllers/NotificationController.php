<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('notifications');
    }

    public function data(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('dismissed', false)
            ->latest()
            ->get()
            ->map(function ($notif) {
                return [
                    'id'          => $notif->id,
                    'title'       => $notif->title,
                    'description' => $notif->description,
                    'read'        => $notif->read,
                    'created_at'  => $notif->created_at->toIso8601String(),
                ];
            });

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('dismissed', false)
            ->where('read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function dismiss(Request $request, Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->markAsDismissed();

        return response()->json(['message' => 'Notification dismissed.']);
    }

    public function clearAll(Request $request)
    {
        Notification::where('user_id', auth()->id())
            ->where('dismissed', false)
            ->update(['dismissed' => true, 'dismissed_at' => now()]);

        return response()->json(['message' => 'All notifications cleared.']);
    }

    public function archive(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function ($notif) {
                return [
                    'id'          => $notif->id,
                    'title'       => $notif->title,
                    'description' => $notif->description,
                    'read'        => $notif->read,
                    'created_at'  => $notif->created_at->toIso8601String(),
                ];
            });

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('dismissed', false)
            ->where('read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }
}
