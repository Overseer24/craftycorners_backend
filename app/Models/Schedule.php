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
        'end',
        'endRecur',
        'startTime',
        'endTime',
        'start_recur',
        'startRecur',
        'backgroundColor',
        'daysOfWeek '
    ];

    protected $casts = [
        'start' => 'datetime:Y-m-d H:i',
        'end' => 'datetime:Y-m-d H:i',
        'daysOfWeek '=> 'array'


    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
