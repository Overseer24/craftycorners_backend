<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, searchable, SoftDeletes;

    // protected $with = ['user:id,first_name,last_name,middle_name,user_name,profile_picture', 'community:id,name,community_photo', 'comments.user:id'];

    protected $fillable = [ 'community_id','title' ,'content', 'image', 'video', 'link','post_type', 'video', 'likes_count', 'shares_count','notifiable','subtopics'];


    protected static function booted()
    {
        static::deleting(function ($post) {
            DB::table('notifications')->where('type', 'App\\Notifications\\PostComments')->where('data', 'like', '%"post_id":'.$post->id.'%')->delete();

            DB::table('notifications')->where('type', 'App\\Notifications\\PostLiked')->where('data', 'like', '%"post_id":'.$post->id.'%')->delete();
        });
    }


    protected static function boot()
    {
        parent::boot();
        static::created(function($post){
            $post->updatePostLikesCount();
//            Cache::tags(['posts', 'homepage-posts'])->flush();
        });
        static::updated(function($post){
//            Cache::tags(['post', $post->id, 'posts', 'homepage-posts'])->flush();
        });
        static::deleted(function($post){
            $post->updatePostLikesCount();
//            Cache::tags(['posts', 'homepage-posts'])->flush();
        });
    }

    protected $casts = [
       'notifiable'=>'boolean'
    ];

    public function toSearchableArray()
    {
       return ['id' => $this->id,
           'title' => $this->title
           ];
    }


    public function updatePostLikesCount()
    {
        $this->update(['likes_count' => $this->likes()->count()]);
    }

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

    // Check if the post was liked by a specific user
    public function wasLikedByUser($user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    // Mark the post as liked by a specific user
    public function markAsLikedByUser($user)
    {
        if (!$this->wasLikedByUser($user)) {
            $this->likes()->attach($user);
            $this->updatePostLikesCount();
        }
    }




}
