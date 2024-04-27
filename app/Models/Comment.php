<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable=['content','user_id','post_id'];




    protected static function boot()
    {
        parent::boot();
        static::created(function($comment){
            $comment->post->increment('comments_count');
        });
        static::deleted(function($comment){
            $comment->post->decrement('comments_count');
        });
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
