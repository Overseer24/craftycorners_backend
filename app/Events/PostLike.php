<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostLike implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

   public $post;
    public function __construct($post)
    {

        $this->post = $post;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-' . $this->post->user_id),
        ];
    }

//    public function broadcastWith(): array
//    {
//        return [
//            'post' => $this->post,
//        ];
//    }
}
