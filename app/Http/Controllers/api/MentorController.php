<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\MentorApplicationRequest;
use App\Http\Resources\Mentor\AuthApprovedMentor;
use App\Http\Resources\Mentor\MentorsCommunityResource;
use App\Http\Resources\Mentor\SpecificApplicationResource;
use App\Http\Resources\Mentor\SpecificApprovedMentors;
use App\Notifications\MentorLikeNotification;
use App\Notifications\MentorshipApplicationStatus;
use App\Models\Community;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\Mentor\ViewApplicationResource;
use function PHPUnit\Framework\isTrue;

class MentorController extends Controller
{

    public function likeMentor(Mentor $mentor)
    {
        //store like information to post_like table
        $liker = auth()->user();
        //ensure that the user is mentor to be liked
        if ($mentor->mentor_likes()->where('user_id', $liker->id)->exists()) {
            return response()->json([
                'message' => 'You have already liked this mentor'
            ], 400);
        }

      $existingNotification = $mentor->user->notifications()
            ->where('type', MentorLikeNotification::class)
            ->where('notifiable_id', $mentor->user->id)
            ->where('notifiable_type', get_class($mentor->user))
            ->where('data->liker_id', $liker->id)
            ->first();

        if (!$existingNotification) {
            $mentor->user->notify(new MentorLikeNotification($mentor, $liker));
            broadcast(new \App\Events\MentorLikeNotification($mentor, $liker))->toOthers();
        }



        $mentor->increment('like_counts');
        $liker->mentor_likes()->attach($mentor);
        return response()->json([
            'message' => 'Mentor liked successfully'
        ]);
    }
    public function unlikeMentor(Mentor $mentor)
    {
        $unliker = auth()->user();

        if (!$mentor->mentor_likes()->where('user_id', $unliker->id)->exists()) {
            return response()->json([
                'message' => 'You have not liked this mentor'
            ], 400);
        }
        $unliker->mentor_likes()->detach($mentor);
        $mentor->decrement('like_counts');
        return response()->json([
            'message' => 'Mentor unliked successfully'
        ]);
    }



    public function getUserMentor(User $user){
        //get only the mentor if the user is a mentor

        $mentorship = $user->mentor()->with('community')->where('status', 'approved')->get();
        return response()->json([
            'mentorship' => $mentorship->map(function ($mentorship){
                return new AuthApprovedMentor($mentorship);
            })
        ]);
    }

    //show auth mentor
    public function showAuthMentor()
    {
        $user = auth()->user();

        if ($user->type !== 'mentor') {
            return response()->json([
                'message' => 'User is not a mentor'
            ], 404);
        }

        //then show all the mentorship of the user that are status approved
        $mentor = $user->mentor()->with('community')->where('status', 'approved')->get();
        return response()->json([
            'data' => $mentor->map(function ($mentor){
                return new AuthApprovedMentor($mentor);
            })
        ]);
    }



    public function showApprovedMentors()
    {
        $mentors = Mentor::with('user','community')
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->where('status', 'approved')->get();
        return response()->json($mentors->map(function ($mentor){
            return new SpecificApprovedMentors($mentor);
        }));
    }


    public function applyForMentorship(MentorApplicationRequest $request){

        $user = auth()->user();
        //make sure that the user is joined in the community


        $requestData = array_merge($request->validated(), [
            'program' => $user->program,
            'student_id' => $user->student_id,
        ]);

        //check if user already applies for mentorship accept if rejected stop if approved and pending
        $mentor = $user->mentor()->where('community_id', $request->community_id)
            ->where(function ($query) {
              $query->where('status', 'pending')
                  ->orWhere('status', 'approved')
                  ->orWhere('status', 'for assessment');
            })
            ->first();

        if($mentor){
            return response()->json(
                [
                    'message' => 'You have already applied for mentorship in this community'
                ], 400
            );
        }

        $user->mentor()->create($requestData);
        //auto add the users student id to the f
        return response()->json([
            'message' => 'Mentorship application submitted successfully',
        ], 201);
    }

    public function viewApplications(){

        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'message' => 'You are not authorized to view this page'
            ], 403);
        }
        $applications = Mentor::with('user','community')
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();

        return ViewApplicationResource::collection($applications);
    }

    public function showApplication(Mentor $mentor){
        if(!auth()->user()->type == 'admin'and $mentor->user_id !== auth()->user()->id){
            return response()->json([
                'message' => 'You are not authorized to view this page'
            ], 403);
        }
        //load
        $mentor->load('user','community');
        return new SpecificApplicationResource($mentor);
    }

    public function approveApplication(Mentor $mentor){

        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'message' => 'You are not authorized to approve this application'
            ], 403);
        }
        //check if user has already been approved in that community that he/she applied for
        $mentorship = Mentor::where('user_id', $mentor->user_id)
            ->where('community_id', $mentor->community_id)
            ->where('status', 'approved')
            ->first();

        if($mentorship){
            return response()->json([
                'message' => 'User has already been approved in this community'
            ], 400);
        }
        broadcast(new \App\Events\MentorshipApplicationStatus($mentor, 'approved'))->toOthers();
        $mentor->user->notify(new MentorshipApplicationStatus($mentor, 'approved'));
//        Mail::to($mentor->user->email)->send(new MentorshipApplicationStatus($mentor, 'approved', $mentor->user));
        $mentor->user->update([
            'type' => 'mentor'
        ]);
        $mentor->update([
            'status' => 'approved'
        ]);
        //send email or live notification to the user


        return response()->json([
            'message' => 'Application approved successfully'
        ]);
    }

    public function showMentorsOfCommunity(Community $community){
        //show apporve mentor of community
        $mentors = $community->mentor()->where('status', 'approved')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })->orderBy('like_counts', 'desc')
            ->get();

//
//        return response()->json($mentors);
        return response()->json($mentors->map(function ($mentor){
            return new MentorsCommunityResource($mentor);
        }));


    }



    //show auth user communities where he/she is a mentor
    public function showAuthUserMentorCommunities()
    {
        $user = auth()->user();
        //show communities where user is mentor and approved
        $communities = Community::with('mentor')->whereHas('mentor', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', 'approved');
        })
            ->whereHas('user', function ($query) {
            $query->whereNull('deleted_at');
        })
            ->get();

        return response()->json($communities->map(function ($community){
            return [
                'id' => $community->id,
                'name' => $community->name,
            ];
        }));

    }


    public function rejectApplication(Mentor $mentor){


        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'error' => 'You are not authorized to reject this application'
            ], 403);
        }
//        $mentor->update([
//            'status' => 'rejected']);
        broadcast(new \App\Events\MentorshipApplicationStatus($mentor, 'rejected'))->toOthers();
        $mentor->user->notify(new MentorshipApplicationStatus($mentor, 'rejected'));
//        Mail::to($mentor->user->email)->send(new MentorshipApplicationStatus($mentor,'rejected', $mentor->user));
        //send email or live notification to the user

        //delete the application
        $mentor->delete();
        return response()->json([
            'message' => 'Application rejected successfully'
        ]);
    }

    public function revokeMentorship(Mentor $mentor){

        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'message' => 'You are not authorized to revoke mentorship'
            ], 403);
        }

        $mentor->update([
            'status' => 'revoked'
        ]);
        broadcast(new \App\Events\MentorshipApplicationStatus($mentor, 'revoked'))->toOthers();
    $mentor->user->notify(new MentorshipApplicationStatus($mentor, 'revoked'));

        $user = $mentor->user;

        //check if the user has any other approved mentorship
        if($user->mentor()->where('status','approved')->doesntExist()){
            $user->update([
                'type' => 'hobbyist'
            ]);
        }

        return response()->json([
            'message' => 'Mentorship revoked successfully'
        ]);
    }

    public function retireMentorship(Community $community)
    {

        //make sure that the user is the owner of the mentorship
        if (auth()->user()->type !== 'mentor') {
            return response()->json([
                'message' => 'Only mentors can retire mentorship.'
            ], 403);
        }
        //check if user already retired mentorship in that community
        if(auth()->user()->mentor()->where('community_id', $community->id)->where('status', 'retired')->exists()){
            return response()->json([
                'message' => 'You have already retired mentorship in this community'
            ], 400);
        }

        $mentorship = auth()->user()->mentor()->where('community_id', $community->id)->first();
        if (!$mentorship) {
            return response()->json([
                'message' => 'You are not a mentor of this community'
            ], 403);
        }

        $mentorship->update([
            'status' => 'retired'
        ]);
        //check if the user has any other approved mentorship if none return to hobbyist
        if(auth()->user()->mentor()->where('status','approved')->doesntExist()){
            auth()->user()->update([
                'type' => 'hobbyist'
            ]);
        }
        return response()->json([
            'message' => 'Mentorship retired successfully'
        ]);
    }

    public function setAssessmentDate(Request $request, Mentor $mentor){

        //after the application, the admin will set the assessment date then set the status for "for assessment"
        $request->validate([
            'date_of_Assessment' => 'required|date',
        ]);
        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'message' => 'You are not authorized to set assessment date'
            ], 403);
        }
        $mentor->update([
            'date_of_Assessment' => $request->date_of_Assessment,
            'status' => 'for assessment'
        ]);

        broadcast(new \App\Events\MentorshipApplicationStatus($mentor, 'for assessment'))->toOthers();
        $mentor->user->notify(new MentorshipApplicationStatus($mentor, 'for assessment'));
        return response()->json([
            'message' => 'Assessment date set successfully',
            'data' => $mentor
        ]);
    }

    public function cancelApplication(Mentor $mentor){
        if(!auth()->user()){
            return response()->json([
                'message' => 'You are not authorized to cancel this application'
            ], 403);
        }
        $mentor->delete();
        return response()->json([
            'message' => 'Application cancelled successfully'
        ]);
    }

}
