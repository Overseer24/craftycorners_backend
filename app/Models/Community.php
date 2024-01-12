<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'community_photo',
        'cover_photo',
    ];


    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTimestamps();
    }



    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function videos(){
        return $this->hasMany(Video::class);
    }



    public function members_count()
{
    return $this->users()->count();
}

    public function joined(){

        return $this->belongsToMany(User::class,'community_members',)->withTimestamps();
    }

}


