<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostInteraction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $interactionType;
    public function __construct($post, $interactionType)
    {
        $this->post = $post;
        $this->interactionType = $interactionType;

    }


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-' . $this->post->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        $data = [
            'post_id' => $this->post->id,
            'interaction_type' => $this->interactionType,
        ];

        if ($this->interactionType === 'like') {
            // Handle post like notification
            $data['notification_data'] = new \App\Http\Resources\Post\PostLikeNotificationResource($this->post);
        } elseif ($this->interactionType === 'comment') {
            // Handle post comment notification
            $latestComment = $this->post->comments()->latest()->first();
            $data['notification_data'] = new \App\Http\Resources\Comment\CommentResource($latestComment);
        }
        return $data;
    }
}
