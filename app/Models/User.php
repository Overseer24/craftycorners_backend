<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'middle_name',
        'user_name',
        'password',
        'profile_picture',
        'birthday',
        'type',
        'gender',
        'phone_number',
        'student_id',
        'program',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date:Y-m-d',
    ];

    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }

    public function community(){
        return $this->hasMany(Community::class);
    }

    public function setBirthdayAttribute($value)
    {
        $this->attributes['birthday'] = date('Y-m-d', strtotime($value));
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function reports(){
        return $this->hasMany(ReportPost::class);
    }

    public function communities()
    {
        return $this->belongsToMany(Community::class,'community_members')->withTimestamps();
    }

    public function likes()
    {
        return $this->belongsToMany(Post::class, 'post_like')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function mentor(){
        return $this->hasMany(Mentor::class);
    }


    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }


    public function sentMessages(){
        return $this->hasMany(Message::class,'from_user_id');
    }

    public function receivedMessages(){
        return $this->hasMany(Message::class,'to_user_id');
    }



    // public function community_user()
    // {
    //     return $this->belongsToMany(Community::class, 'community_user')->withTimestamps();
    // }


    // public function postShares()
    // {
    //     return $this->hasMany(Post::class);
    // }



}
