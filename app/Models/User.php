<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\PasswordReset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Searchable;

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

public function toSearchableArray(): array
{
    return[
        'id' => $this->id,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'middle_name' => $this->middle_name,
        'user_name' => $this->user_name,
        'type' => $this->type,

    ];
}

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
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];


    public function addExperiencePoints($points, $community_id)
    {
      $experience =$this->experiences()->firstOrCreate(['community_id' => $community_id]);
      $experience->experience_points+= $points;
      $this->checkLevelUp($community_id);
      $experience->save();

      //check if user level up to that community then notify

    }

    private function calculateLevel($experiencePoints)
    {
        return DB::table('levels')->where('experience_required', '<=', $experiencePoints)
            ->orderByDesc('level')
            ->value('level') ?? 1;

    }

    //check if user level up to that community then notify
    private function checkLevelUp($communityId)
    {
        $experience = $this->experiences()->where('community_id', $communityId)->firstOrFail();
        $currentLevel = $this->calculateLevel($experience->experience_points);
        $nextLevelExperience = DB::table('levels')->where('level', $currentLevel + 1)->value('experience_required');
        if ($experience->experience_points >= $nextLevelExperience) {
        $experience->experience_points = 0;
        $experience->save();
        $experience->level = $currentLevel + 1;
//            $this->notify(new UserLevelledUp($currentLevel + 1, $communityId));
            };

    }

//        $experience = $this->experiences()->where('community_id', $communityId)->firstOrFail();
//        $currentLevel = $this->calculateLevel($experience->experience_points);
//        $nextLevelExperience = DB::table('levels')->where('level', $currentLevel + 1)->value('experience_required');
//
//        if ($experience->experience_points >= $nextLevelExperience) {
//            $experience->experience_points = 0;
//            $experience->level = $currentLevel + 1;
//            $experience->save();
//            $this->notify(new UserLevelledUp($currentLevel + 1, $communityId));
//        }

//    public function resetExperiencePoints()
//    {
//        $this->experiences()->update(['experience_points' => 0]);
//    }


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

    public function experiences():HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function getLevel($community_id){
        $experience = $this->experiences();
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

    public function ResetPassword($token)
    {
        $this->notify(new PasswordReset($token));
    }

//    public function sentMessages(){
//        return $this->hasMany(Message::class,'sender_id');
//    }
//
//    public function receivedMessages(){
//        return $this->hasMany(Message::class,'receiver_id');
//    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);

    }


    public function unreadMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id')
            ->where('read', false);
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
