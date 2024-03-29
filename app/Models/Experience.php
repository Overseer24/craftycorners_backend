<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Experience extends Model
{
    protected $fillable=[
        'user_id',
      'community_id',
      'experience_points',
        'level',
        'next_experience_required',
        'badge'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function community(){
        return $this->belongsTo(Community::class);
    }



}
