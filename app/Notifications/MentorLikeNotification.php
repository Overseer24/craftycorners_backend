<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentorLikeNotification extends Notification
{
    use Queueable;

    protected $mentor;
    protected $liker;
    public function __construct($mentor, $liker)
    {
        $this->mentor = $mentor;
        $this->liker = $liker;

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
        //select only mentor id and name
        return [
            'mentor_id' => $this->mentor->id,
            'mentor_first_name' => $this->mentor->first_name,
            'mentor_last_name' => $this->mentor->last_name,
            'liker_id' => $this->liker->id,
            'liker_first_name' => $this->liker->first_name,
            'liker_last_name' => $this->liker->last_name,
        ];
    }
}
