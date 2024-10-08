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

        //latest message if the latest is remove proceed to non deleted
        $latest_message = $this->messages()->where(function ($query)use ($auth_user){
            $query->where('deleted_by', '!=', $auth_user->id)
                ->orWhereNull('deleted_by');
        })->latest()->with('attachments')->first();

        if ($latest_message) {
            try {
                $decryptedMessage = decrypt($latest_message->message);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                $decryptedMessage = $latest_message->message; // Or however you want to handle this case
            }
            $latest_message_data = [
                'id' => $latest_message->id,
                'message' => $decryptedMessage,
                'read' => $latest_message->read,
                'created_at' => $latest_message->created_at->format('Y-m-d H:i:s'),
                'sender_id' => $latest_message->sender_id,
                'receiver_id' => $latest_message->receiver_id,
            ];

            if ($latest_message->attachments && $latest_message->attachments->isNotEmpty()) {
                $attachments = $latest_message->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_path' => $attachment->file_path,
                        'file_type' => $attachment->file_type,
                        'file_name' => $attachment->file_name,
                    ];
                });
                $latest_message_data['attachments'] = $attachments;
            }
        } else {
            $latest_message_data = null;
        }



        return [
            'id' => $this->id,
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

            'latest_message' =>$latest_message_data
];
    }
}
