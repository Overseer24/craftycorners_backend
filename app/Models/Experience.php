<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable=[
        'user_id',
      'community_id',
      'experience_points',
        'level'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function community(){
        return $this->belongsTo(Community::class);
    }
}
