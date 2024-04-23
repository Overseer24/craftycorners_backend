<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailNotification implements ShouldQueue
{
    use Queueable;


    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject('Verify Email Address')
            ->greeting('Hello, ' . ucfirst($notifiable->first_name) . ' ' . ucfirst($notifiable->last_name) . '!')
            ->line('Thank you for signing up with our App. To Ensure the security of your account, please verify your email address.')
            ->action('Verify Email', $this->verificationUrl($notifiable))
            ->line('If your did not create an account, no further action is required.');
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */

}
