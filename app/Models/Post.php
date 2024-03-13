<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // protected $with = ['user:id,first_name,last_name,middle_name,user_name,profile_picture', 'community:id,name,community_photo', 'comments.user:id'];

    protected $fillable = [ 'community_id','title' ,'content', 'image', 'video', 'link','post_type', 'video'];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_like')->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(ReportPost::class);
    }
    // public function postShares()
    // {
    //     return $this->hasMany(PostShare::class);
    // }

    public function getPhotoUrlAttribute()
    {
        return $this->image ? asset('storage/posts/' . $this->image) : null;
    }

    // Increment likes count when a like is created
    public function incrementLikesCount()
    {
        $this->increment('likes_count');
    }
    // Decrement likes count when a like is deleted
    public function decrementLikesCount()
    {
        if ($this->likes_count > 0){
            $this->decrement('likes_count');
        }
    }


    // Increment shares count when a share is created
    public function incrementSharesCount()
    {
        $this->increment('shares_count');
    }

    // Decrement shares count when a share is deleted
    public function decrementSharesCount()
    {
        if ($this->shares_count > 0){
            $this->decrement('shares_count');
        }

    }

    public function post_liker()
    {
        return $this->belongsToMany(User::class, 'post_like');
    }

    // Increment comments count when a comment is created




}
