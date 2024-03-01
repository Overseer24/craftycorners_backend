<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    /**
     * @var mixed|string
     */
    protected $fillable = ['user_id','student_id', 'program', 'community_id', 'date_of_Assessment', 'specialization','status'];

   protected $casts = [
        'date_of_Assessment' => 'datetime:Y-m-d H:i',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
