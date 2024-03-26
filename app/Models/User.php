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
        'pre_assessment_completed' => 'boolean',
    ];

    //check user level in a community default is 1


    //check all user level all across the community that he belongs to
//    public function checkAllUserLevel()
//    {
//        $communities = $this->communities;
//        foreach ($communities as $community) {
//            $this->checkUserLevel($community->id);
//        }
//    }


    //get user next xp required to level up on that community
    public function nextLevelExperience($community_id)
    {
        $currentLevel = $this->calculateLevel($community_id);
        $nextLevelExperience = DB::table('levels')->where('level', $currentLevel+1)->value('experience_required');
        return $nextLevelExperience;
    }

    //get all level of user across all community
    public function allUserLevel()
    {
        $communities = $this->communities;
        $levels = [];
        foreach ($communities as $community) {
            $levels[$community->id] = $this->calculateLevel($community->id);
        }
        return $levels;
    }

    public function addExperiencePoints($points, $community_id)
    {
      $experience =$this->experiences()->firstOrCreate(['community_id' => $community_id]);
      $experience->experience_points+= $points;


      $userLevel = $this->calculateLevel($community_id);
      $experience->level = $userLevel;

      //store badge according to their level refer to levels table
      $experience->badge = DB::table('levels')->where('level', $userLevel)->value('badge');

      $experience->save();
      $this->checkLevelUp($experience,$community_id);

    }

    public function getLevel($community_id){
        return $this->calculateLevel($community_id);
    }

    private function calculateLevel($communityId)
    {
       //check level from experience table then refer to level table
        $userLevel = $this->experiences()->where('level', '>', 0)->where('community_id', $communityId)->firstOrFail();
        return $userLevel->level;
    }

    //check if user level up to that community then notify
    private function checkLevelUp($experience,$communityId)
    {

        $currentLevel = $this->calculateLevel($communityId);
        $nextLevelExperience = DB::table('levels')->where('level', $currentLevel+1)->value('experience_required');

        if ($experience->experience_points >= $nextLevelExperience) {
            //calculate excess experience points
            $excessExperience = $experience->experience_points - $nextLevelExperience;
            $experience->update(['level' => $currentLevel + 1]);
            //update the experience points to 0
            $experience->update(['experience_points' => $excessExperience]);
            //store badge according to their level refer to levels table
            $experience->update(['badge' => DB::table('levels')->where('level', $currentLevel + 1)->value('badge')]);

            //if there are mo excess xp check again if user can level up recursively
            $this->checkLevelUp($experience, $communityId);


        }
    }

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
