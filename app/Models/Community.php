<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Community extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'community_photo',
        'cover_photo',
        'members_count',
    ];


    public function toSearchableArray(): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }


    protected static function boot()
    {
        parent::boot();
        static::created(function($community){
            $community->updateMembersCount();
        });
        static::deleted(function($community){
            $community->updateMembersCount();
        });
    }

    public function updateMembersCount(){
        $this->update(['members_count'=>$this->joined()->count()]);
    }


    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentor(){
        return $this->hasMany(Mentor::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function videos(){
        return $this->hasMany(Video::class);
    }

    // public function likes()
    // {
    //     return $this->belongsToMany(User::class, 'community_like')->withTimestamps();
    // }


    public function members_count()
{
    return $this->users()->count();
}

    public function joined(){

        return $this->belongsToMany(User::class,'community_members',)->withTimestamps();
    }

    public function joinedUsers()
    {
        return $this->belongsToMany(User::class, 'community_members', 'community_id', 'user_id');
    }

}


