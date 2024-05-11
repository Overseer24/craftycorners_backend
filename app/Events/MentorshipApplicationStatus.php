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

class MentorshipApplicationStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $mentor;
    protected $status;
    public function __construct($mentor, $status)
    {
        $this->mentor = $mentor;
        $this->status = $status;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn():array
    {
        return[new PrivateChannel('user-' . $this->mentor->user_id)];

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
                    It will be at '. Carbon::parse($this->mentor->date_of_Assessment)->format('F j, Y g:i A')
            ];
        }
        return [];
    }
}
