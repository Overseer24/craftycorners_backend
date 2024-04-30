<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Schedule extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'title',
        'start',
        'endRecur',
        'startTime',
        'endTime',
        'start_recur',
        'startRecur',
        'backgroundColor',
        'daysofweek'
    ];

    protected $casts = [
        'start' => 'datetime:Y-m-d H:i',
        'end' => 'datetime:Y-m-d H:i',
        'daysofweek'=> 'array'


    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
