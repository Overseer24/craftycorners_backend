<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Message extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'sender_id','receiver_id', 'message', 'deleted_by','has_attachment','read'];


    protected $casts = [
        'read' => 'boolean',
    ];
    protected static function boot()
    {
        parent::boot();

        static::created(function ($message){
            $message->conversation->increment('messages_count');
        });

        static::deleted(function ($message){
            $message->conversation->decrement('messages_count');
        });
    }





    public function conversation()
    {
         return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function markAsread($conversationId, $userId)
    {
        $this->where('conversation_id', $conversationId)
            ->where('receiver_id', $userId)
            ->where('read', false)
            ->update(['read' => true]);

    }




}
