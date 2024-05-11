<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MentorshipApplicationStatus extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $mentor;
    protected $status;
    /**
     * Create a new notification instance.
     */
    public function __construct($mentor, $status)
    {
        $this->mentor = $mentor;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === 'approved') {
            return $this->approvedNotification($notifiable);
        }

        elseif ($this->status === 'rejected') {
            return $this->rejectedNotification($notifiable);
        }

        elseif ($this->status==='revoked'){
            return $this->revokedNotification($notifiable);
        }
        elseif($this->status === 'for assessment'){
            return $this->forAssessmentNotification($notifiable);
        }

        return (new MailMessage);
    }

    private function forAssessmentNotification(object $notifiable)
    {
        return (new MailMessage)
            ->subject('Mentorship Application Status')
            ->greeting('Hello, '. ucfirst($notifiable->first_name) . ' ' . ucfirst($notifiable->last_name) . '!')
            //add assessment date
            ->line('The date for you assessment has been scheduled')
            ->line('It will be at'. $this->mentor->date_of_Assessment)
            ->line('Please bring along the necessary documents')
            ->line('Thank you for your interest in the mentorship program');
    }

    private function revokedNotification(object $notifiable):MailMessage
    {
        return (new MailMessage)
            ->subject('Mentorship Application Status')
            ->greeting('Hello, '. ucfirst($notifiable->first_name) . ' ' . ucfirst($notifiable->last_name) . '!')
            ->line('We are sorry to inform you that your mentorship application has been revoked.')
            ->line('Thank you for being part of the mentorship');
    }

    private function approvedNotification(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mentorship Application Status')
            ->greeting('Hello, '. ucfirst($notifiable->first_name) . ' ' . ucfirst($notifiable->last_name) . '!')
            ->line('Congratulations! Your mentorship application has been approved.')
            ->line('Thank you for your interest in the mentorship program');
    }

    private function rejectedNotification(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mentorship Application Status')
            ->greeting('Hello, '. ucfirst($notifiable->first_name) . ' ' . ucfirst($notifiable->last_name) . '!')
            ->line('We are sorry to inform you that your mentorship application has been rejected.')
            ->line('Thank you for your interest in the mentorship program');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
      if ($this->status === 'approved') {
        return [
            'status' => $this->status,
            'message' => 'Your mentorship application has been approved',
        ];
      }

        if ($this->status === 'rejected') {
            return [
                'status' => $this->status,
                'message' => 'Your mentorship application has been rejected',
            ];
        }

        if ($this->status === 'revoked') {
            return [
                'status' => $this->status,
                'message' => 'Your mentorship application has been revoked',
            ];
        }

        if ($this->status === 'for assessment') {
            return [
                'status' => $this->status,
                'message' => 'The date for you assessment has been scheduled.
                    It will be at'. $this->mentor->date_of_Assessment,
            ];
        }

        return [];
    }

    public function broadcastOn():array
    {
        return[new PrivateChannel('user-' . $this->mentor->id)];

    }

    public function broadcastWith():array
    {
        if ($this->status === 'approved') {
            return [
                'status' => $this->status,
                'message' => 'Your mentorship application has been approved',
            ];
        }

        if ($this->status === 'rejected') {
            return [
                'status' => $this->status,
                'message' => 'Your mentorship application has been rejected',
            ];
        }

        if ($this->status === 'revoked') {
            return [
                'status' => $this->status,
                'message' => 'Your mentorship application has been revoked',
            ];
        }

        if ($this->status === 'for assessment') {
            return [
                'status' => $this->status,
                'message' => 'The date for you assessment has been scheduled.
                    It will be at'. $this->mentor->date_of_Assessment,
            ];
        }
        return [];
    }
}
