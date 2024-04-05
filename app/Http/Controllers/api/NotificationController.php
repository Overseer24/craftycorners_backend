<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->unreadNotifications;

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            Cache::forget('unreadNotificationsCount-' . auth()->id());
            return response()->json(['message' => 'Notification marked as read']);
        }
        return response()->json(['message' => 'Notification not found'], 404);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        Cache::forget('unreadNotificationsCount-' . auth()->id());
        return response()->json(['message' => 'All notifications marked as read']);
    }

}
