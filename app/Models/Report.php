<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reported_user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'is_resolved',
        'resolved_by',
        'resolved_at',
        'resolution_option',
        'unsuspend_date',
        'resolution_description',
        'proof'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reportable()
    {
        return $this->morphTo();
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
