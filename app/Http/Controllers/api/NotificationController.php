<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        $user = auth()->user();
        $notifications = $user->notifications()
            ->with(['notifiable', 'relatedUser','notifiable.community'])
//            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')->paginate(5);

        $transformedNotifications = collect([]);

        $notifications->groupBy('notifiable_id')->each(function ($groupedNotifications) use ($transformedNotifications) {
            $notification = $groupedNotifications->first(); // Take the first notification in the group
            $data = [
                'id' => $notification->id,
                'type' => $notification->type,
                'read_at' => $notification->read_at,
                'post' => [
                    'id' => $notification->notifiable->id,
                    'title' => $notification->notifiable->title,


                ],
                'community'=>[
                    'id'=>$notification->notifiable->community->id,
                    'name'=>$notification->notifiable->community->name,
                ],
                'like' => [],
            ];

            if ($notification->type === 'post_like') {
                $groupedNotifications->each(function ($notification) use (&$data) {
                    $data['like'][] = [
                        'id' => $notification->relatedUser->id,
                        'first_name' => $notification->relatedUser->first_name,
                        'last_name' => $notification->relatedUser->last_name,
                        'profile_picture' => $notification->relatedUser->profile_picture,
                    ];
                });
            }

            $transformedNotifications->push($data);
        });

        return response()->json($transformedNotifications);
    }

    public function markAsRead($notificationId){
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if($notification){
            $notification->markAsRead();
            return response()->json([
                'message' => 'Notification marked as read',
            ]);
        }
        return response()->json([
            'message' => 'Notification not found',
        ], 404);
    }
}
