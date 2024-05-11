<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use function PHPUnit\Framework\returnArgument;

class ReportResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;
    protected $resolutionOption;
    protected $unsuspendDate;
    /**
     * Create a new notification instance.
     */
    public function __construct($report,$resolutionOption, $unsuspendDate)
    {
        $this->report = $report;
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
        return ['mail', 'database'];
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
            ->line('Your content has been reported for inappropriate content')
            ->line('You have received a warning for violating our community guidelines.')
            ->line('Delete the reported content to avoid further issues.')
            ->line('Please review our community guidelines to avoid further issues.')
            ->line('If you have any questions, please contact us.');
    }

    protected function suspendNotification():MailMessage
    {
       return(new MailMessage)
            ->subject('Suspension Notification')
            ->line('Your content has been reported for inappropriate content')
            ->line('You have repeatedly violated our community guidelines. Your account has been suspended.')
            ->line('Your account will be unsuspended on: ' .Carbon::parse($this->unsuspendDate)->format('F j, Y g:i A'))
            ->line('If you have any questions, please contact us.');



    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->resolutionOption === 'warn') {
            return [
                'message' => 'Your content has been reported for inappropriate content. You have received a warning for violating our community guidelines. Delete the reported content to avoid further issues. Please review our community guidelines to avoid further issues. If you have any questions, please contact us.'
            ];
        }
            elseif ($this->resolutionOption === 'suspend') {
                return [
                    'message' => 'Your content has been reported for inappropriate content. You have repeatedly violated our community guidelines. Your account has been suspended. Your account will be unsuspended on: ' .Carbon::parse($this->unsuspendDate)->format('F j, Y g:i A') . 'If you have any questions, please contact us.'
                ];
            }
        return [];

    }

//    public function broadcastOn(): array
//    {
//        return [
//            new PrivateChannel('user-' . $this->report->reported_user_id),
//        ];
//    }
//
//    public function broadcastWith(): array
//    {
//     if ($this->resolutionOption === 'warn') {
//         return [
//             'message' => 'Your content has been reported for inappropriate content. You have received a warning for violating our community guidelines. Please review our community guidelines to avoid further issues. If you have any questions, please contact us.'
//         ];
//     }
//        elseif ($this->resolutionOption === 'suspend') {
//            return [
//                'message' => 'Your content has been reported for inappropriate content. You have repeatedly violated our community guidelines. Your account has been suspended. Your account will be unsuspended on: ' .Carbon::parse($this->unsuspendDate)->format('F j, Y g:i A') . 'If you have any questions, please contact us.'
//            ];
//        }
//
//        return [];
//    }

}
