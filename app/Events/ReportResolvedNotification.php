<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ReportResolvedNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $report;
    protected $resolutionOption;
    protected $unsuspendDate;
    public function __construct($report, $resolutionOption, $unsuspendDate)
    {
        $this->report = $report;
        $this->resolutionOption = $resolutionOption;
        $this->unsuspendDate = $unsuspendDate;
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-' . $this->report->reported_user_id),
        ];
    }

    public function broadcastWith(): array
    {
     if ($this->resolutionOption === 'warn') {
         return [
             'message' => 'Your content has been reported for inappropriate content. You have received a warning for violating our community guidelines. Please review our community guidelines to avoid further issues. If you have any questions, please contact us.'
         ];
     }
        elseif ($this->resolutionOption === 'suspend') {
            return [
                'message' => 'Your content has been reported for inappropriate content. You have repeatedly violated our community guidelines. Your account has been suspended. Your account will be unsuspended on: ' .Carbon::parse($this->unsuspendDate)->format('F j, Y g:i A') . 'If you have any questions, please contact us.'
            ];
        }

        return [];
    }
}
