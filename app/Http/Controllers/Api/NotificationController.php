<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::forUser(Auth::user())
            ->latest()
            ->paginate(10);

        return response()->json($notifications);
    }

    public function unread()
    {
        $count = Notification::forUser(Auth::user())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAsRead(Request $request)
    {
        if ($request->has('notification_id')) {
            $notification = Notification::findOrFail($request->notification_id);
            $notification->markAsRead();
        } else {
            Notification::forUser(Auth::user())
                ->unread()
                ->update(['read_at' => now()]);
        }

        return response()->json(['message' => 'Notifications marked as read']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'role' => 'nullable|in:admin,participant,validator',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $notification = Notification::create($request->all());

        return response()->json($notification, 201);
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response()->json(['message' => 'Notification deleted']);
    }
}
