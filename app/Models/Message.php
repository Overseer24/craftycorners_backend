<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id','receiver_id','message','read'];

    public function sender(){
        return $this->belongsTo(User::class,'from_user_id');
    }

    public function receiver(){
        return $this->belongsTo(User::class,'to_user_id');
    }
}
