<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReporterResolve extends Notification implements ShouldQueue
{
    use Queueable;


    protected $report;
    /**
     * Create a new notification instance.
     */
    public function __construct($report)
    {
        $this->report = $report;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->report->resolution_option === 'warn') {
            return $this->warnNotification($notifiable);
        }

        elseif ($this->report->resolution_option === 'suspend') {
            return $this->suspendNotification($notifiable);
        }

        elseif($this->report->resolution_option === 'ignore') {
            return $this->ignoreNotification($notifiable);
        }

        return (new MailMessage);

    }



    protected function suspendNotification(object $notifiable):MailMessage
    {
        return (new MailMessage)
            ->subject('Report Resolved')
            ->greeting('Hello, ' . ucfirst($notifiable->first_name) . ' ' . ucfirst($notifiable->last_name) . '!')
            ->line('Your report has been resolved.')
            ->line('The user has been suspended for violating our community guidelines.')
            ->line('Thank you for reporting the post.')
            ->line('If you have any further questions, please contact us');
    }

    protected function warnNotification(object $notifiable):MailMessage
    {
        return (new MailMessage)
            ->subject('Report Resolved')
            ->greeting('Hello, ' . ucfirst($notifiable->first_name) . ' ' . ucfirst($notifiable->last_name) . '!')
            ->line('Your report has been resolved.')
            ->line('The user has been warned for violating our community guidelines.')
            ->line('Thank you for reporting the post.')
            ->line('If you have any further questions, please contact us');
    }

    protected function ignoreNotification(object $notifiable):MailMessage
    {
        return (new MailMessage)
            ->subject('Report Resolved')
            ->greeting('Hello, ' . ucfirst($notifiable->first_name) . ' ' . ucfirst($notifiable->last_name) . '!')
            ->line('Your report has been resolved.')
            ->line('The report you submitted does not violate our community guidelines.')
            ->line('Thank you for reporting the post.')
            ->line('If you have any further questions, please contact us');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->report->resolution_option === 'warn') {
            return [
                'message' => 'Your report has been resolved. The user has been warned for violating our community guidelines.',
                'report_id' => $this->report->id,
            ];
        }

        elseif ($this->report->resolution_option === 'suspend') {
            return [
                'message' => 'Your report has been resolved. The user has been suspended for violating our community guidelines.',
                'report_id' => $this->report->id,
            ];
        }

        elseif($this->report->resolution_option === 'ignore') {
            return [
                'message' => 'Your report has been resolved. The report you submitted does not violate our community guidelines.',
                'report_id' => $this->report->id,
            ];
        }
        return [];

    }

    /**
     * Get the channels the notification should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */

//    public function broadcastOn(): array
//    {
//        return [
//            new PrivateChannel('user-' . $this->report->user_id),
//        ];
//    }
//
//    /**
//     * Get the data to broadcast.
//     *
//     * @return array<string, mixed>
//     */
//    public function broadcastWith(): array{
//        if ($this->report->resolution_option === 'warn') {
//            return [
//                'message' => 'Your report has been resolved. The user has been warned for violating our community guidelines.',
//                'report_id' => $this->report->id,
//            ];
//        }
//
//        elseif ($this->report->resolution_option === 'suspend') {
//            return [
//                'message' => 'Your report has been resolved. The user has been suspended for violating our community guidelines.',
//                'report_id' => $this->report->id,
//            ];
//        }
//
//        elseif($this->report->resolution_option === 'ignore') {
//            return [
//                'message' => 'Your report has been resolved. The report you submitted does not violate our community guidelines.',
//                'report_id' => $this->report->id,
//            ];
//        }
//        return [];
//    }
//    public function broadcastAs(): string
//    {
//        return 'report-resolved';
//
//    }
}
