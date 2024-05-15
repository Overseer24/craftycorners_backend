<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MentorLikeNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $mentor;
    protected $liker;
    public function __construct($mentor, $liker)
    {
        $this->mentor = $mentor;
        $this->liker = $liker;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-'.$this->mentor->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
           'message'=> 'You get a like from '.$this->liker->first_name.' '.$this->liker->last_name.'.',
        ];
    }
}
