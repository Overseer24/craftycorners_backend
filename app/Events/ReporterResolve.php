<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReporterResolve implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-' . $this->report->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array{
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

}
