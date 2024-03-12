<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user, $message, $conversation;



    /**
     * Create a new event instance.
     */
    public function __construct($user,$message)
    {
        $this->user = $user;
        $this->message = $message;


    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {


        $userId = $this->message->sender_id;
        return [
            new PrivateChannel('conversation-' . $this->message->conversation_id),
            new PrivateChannel('user-' . $this->user),
        ];
    }


    public function broadcastWith()
    {
        return [
            'user' => $this->user,
            'message' => $this->message

        ];
    }

}
