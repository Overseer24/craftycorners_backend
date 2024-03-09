<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
//    protected $authUser_id;
//
//    public function __construct($resource, $authUser_id)
//    {
//        parent::__construct($resource);
//        $this->authUser_id = $authUser_id;
//    }

    public function toArray($request): array
    {
        $auth_user = auth()->id();
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'message' => $this->message,
            'read' => $this->read,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'receiver'=>[
                'receiver_id' => $this->receiver_id,
                'first_name' => $this->receiver->first_name,
                'last_name' => $this->receiver->last_name,
            ],

            ];
    }
}
