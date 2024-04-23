<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'reason',
        'description',
        'is_resolved',
        'resolved_by',
        'resolved_at',
        'resolution_description',
        'resolution_option',
        'unsuspend_date',
        'reported_user_id'
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }
}
