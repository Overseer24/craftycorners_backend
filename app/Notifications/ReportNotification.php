<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportNotification extends Notification
{
    use Queueable;

    protected $report;
    protected $post;

    /**
     * Create a new notification instance.
     */
    public function __construct($report, $post)
    {
        $this->report = $report;
        $this->post = $post;

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


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->report->id,
            'reason' => $this->report->reason,
            'description' => $this->report->description,
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'content' => $this->post->content,
            'image' => $this->post->image,
            'video' => $this->post->video,
            'post_type' => $this->post->post_type,
            'created_at' => $this->post->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->post->updated_at->format('Y-m-d H:i:s'),
            'reported_by' => [
                'id' => $this->report->user->id,
                'first_name' => $this->report->user->first_name,
                'middle_name' => $this->report->user->middle_name,
                'last_name' => $this->report->user->last_name,
                'user_name' => $this->report->user->user_name,
                'program' => $this->report->user->program,
                'student_id' => $this->report->user->student_id,
            ],
            'reported_user' => [
                'first_name' => $this->post->user->first_name,
                'middle_name' => $this->post->user->middle_name,
                'last_name' => $this->post->user->last_name,
                'user_name' => $this->post->user->user_name,
                'program' => $this->post->user->program,
                'student_id' => $this->post->user->student_id,
            ],
        ];
    }
}
