<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostComments extends Notification
{
    use Queueable;

    public $post;
    public $user;
    public $comment;
    /**
     * Create a new notification instance.
     */
    public function __construct($post, $user, $comment)
    {
        $this->post = $post;
        $this->user = $user;
        $this->comment = $comment;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
               'user_id' => $this->user->id,
                'user_name' => $this->user->name,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'profile_picture' => $this->user->profile_picture,
                'post_id' => $this->post->id,
                'community'=>[
                    'id' => $this->post->community->id,
                    'name' => $this->post->community->name,

                ],

        ];
    }
    /**
     * Get the type of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
}
