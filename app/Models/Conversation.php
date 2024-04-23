<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id'];


    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function messageExcludingDeletedBy($user_id)
    {
        return $this->hasMany(Message::class)
            ->where('conversation_id', $this->id) // Ensure messages belong to the current conversation
            ->where(function ($query) use ($user_id) {
                $query->where('deleted_by', '!=', $user_id)
                    ->orWhereNull('deleted_by');
            });// Ensure messages are not deleted by the user
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isRead(): bool
    {
        return $this->read !=null;
    }

//    public function messagesCount()
//    {
//        return $this->hasOne(Message::class)
//            ->selectRaw('conversation_id, count(*) as count')
//            ->groupBy('conversation_id');
//    }
}
