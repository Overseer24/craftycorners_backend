<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $auth_user = auth()->user();
//
        //check if auth user is receiver or sender then use the receiver photo
        if($auth_user->id == $this->receiver_id){
            $photo = $this->sender->profile_picture ?? 'default.jpg';
        }
        else{
            $photo = $this->receiver->profile_picture ?? 'default.jpg';
        }

        return [
            'id' => $this->id,
            'read' => $this->isRead(),
            'receiver_profile_picture' => $photo,
            'user_0'=>[
                'id' => $this->sender_id,
                'first_name' => $this->sender->first_name,
                'last_name' => $this->sender->last_name,
            ],
            'user_1'=>[
                'id' => $this->receiver_id,
                'first_name' => $this->receiver->first_name,
                'last_name' => $this->receiver->last_name,
            ],
            //use map to load other info in messages
            'last_message' => $this->messages->map(function($message){
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'message' => $message->message,
                    'read' => $message->read,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                ];
            })->last(),
        ];


    }
}
