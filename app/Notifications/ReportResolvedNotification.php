<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    protected $resolutionOption;
    protected $unsuspendDate;
    /**
     * Create a new notification instance.
     */
    public function __construct($resolutionOption, $unsuspendDate)
    {
        $this->resolutionOption = $resolutionOption;
        $this->unsuspendDate = $unsuspendDate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->resolutionOption === 'warn') {
            return $this->warnNotification();
        }

        elseif ($this->resolutionOption === 'suspend') {
            return $this->suspendNotification();
        }

        return (new MailMessage);
    }


    protected function warnNotification():MailMessage
    {
        return (new MailMessage)
            ->subject('Warning Notification')
            ->line('Your post has been reported for inappropriate content')
            ->line('You have received a warning for violating our community guidelines.')
            ->line('Please review our community guidelines to avoid further issues.')
            ->line('If you have any questions, please contact us.');
    }

    protected function suspendNotification():MailMessage
    {
        $message = (new MailMessage)
            ->subject('Suspension Notification')
            ->line('Your post has been reported for inappropriate content')
            ->line('You have repeatedly violated our community guidelines. Your account has been suspended.')
            ->line('If you have any questions, please contact us.');

        if ($this->unsuspendDate) {
            $message->line('Your account will be unsuspended on: ' . $this->unsuspendDate->toDateTimeString());
        }
        $message->line('If you have any questions, please contact us.');
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
