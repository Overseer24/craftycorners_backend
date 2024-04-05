<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification implements ShouldQueue
{
    use Queueable;

    public $post;
    public $user;

    public function __construct($post,$user)
    {
        $this->post = $post;
        $this->user = $user;

    }
    /**
     * Get the channels the notification should broadcast on.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
//        $likedUsers = $this->post->likes->map(function ($like) {
//            return [
//                'id' => $like->id,
//                'first_name' => $like->first_name,
//                'last_name' => $like->last_name,
//                'profile_picture' => $like->profile_picture,
//            ];
//        });

        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->user_name,
            'first_name' => $this->user->first_name,
            'middle_name' => $this->user->middle_name,
            'last_name' => $this->user->last_name,
            'profile_picture' => $this->user->profile_picture,
            'post' => [
                'title' => $this->post->title,

            ],
            'community'=>[
                'name' => $this->post->community->name,
            ],

        ];
    }
    /**
     * Get the channels the notification should broadcast on.
     *
     * @param  mixed  $notifiable
     * @return array
     */




/* Get the type of the notification.
*
* @param  mixed  $notifiable
* @return string
*/
}
