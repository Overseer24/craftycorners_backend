<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostShared extends Notification implements ShouldQueue
{
    use Queueable;
    public $post;
    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($post, $user)
    {
        $this->post = $post;
        $this->user = $user;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->user_name,
            'first_name' => $this->user->first_name,
            'middle_name' => $this->user->middle_name,
            'last_name' => $this->user->last_name,
            'profile_picture' => $this->user->profile_picture,
            'post_id' => $this->post->id,
            'community_name' => $this->post->community->name,
        ];
    }
}
