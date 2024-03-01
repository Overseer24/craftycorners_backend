<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Schedule extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'schedule_name',
        'schedule_description',
        'schedule_color',
        'schedule_day',
        'start',
        'end',
    ];

    protected $casts = [
        'start' => 'Y-m-d H:i:s',
        'end' => 'Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
