<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);


        $frontendUrl = config('app.frontend_url');


        $redirectUrl = $frontendUrl . '/email/verify?token=' . urlencode($verificationUrl);

        return (new MailMessage)
        ->subject('Verify Email Address')
        ->line('Hello, ' . $notifiable->first_name . ' ' . $notifiable->last_name . '!')
        ->line('Thank you for signing up with our App. To Ensure the security of your account, please verify your email address.')
        ->action('Verify Email', $this->verificationUrl($notifiable))
        ->line('If your did not create an account, no further action is required.');
    }
}
