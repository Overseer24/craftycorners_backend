<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\MentorApplicationRequest;
use App\Http\Resources\Mentor\AuthApprovedMentor;
use App\Http\Resources\Mentor\SpecificApplicationResource;
use App\Http\Resources\Mentor\SpecificApprovedMentors;
use App\Mail\MentorshipApplicationStatus;
use App\Models\Community;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\Mentor\ViewApplicationResource;

class MentorController extends Controller
{

    public function getUserMentor(User $user){
        //get only the mentor if the user is a mentor
        if ($user->type !== 'mentor') {
            return response()->json([
                'message' => 'User is not a mentor'
            ], 404);
        }

        $mentor = $user->mentor()->with('community')->first();
        return response()->json([
            'id'=>$mentor->id,
            'mentor'=>[
                'user_id' => $mentor->user->id,
                'user_name' => $mentor->user->user_name,
                'first_name' => $mentor->user->first_name,
                'middle_name' =>$mentor->user->middle_name,
                'last_name' => $mentor->user->last_name,
                'email' => $mentor->user->email,
                'profile_picture' => $mentor->user->profile_picture,
            ],
            'community_id'=>[
                'id'=>$mentor->community->id,
                'name'=>$mentor->community->name,
            ],
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


//    public function showAllMentors()
//    {
//        $mentors = Mentor::with('user','community')->get();
//        return response()->json([
//            'data' => $mentors
//        ]);
//    }

    public function showApprovedMentors()
    {
        $mentors = Mentor::with('user','community')->where('status', 'approved')->get();


       return response()->json($mentors->map(function ($mentor){
           return new SpecificApprovedMentors($mentor);
       }));
    }


    public function applyForMentorship(MentorApplicationRequest $request){

        $user = auth()->user();

        $requestData = array_merge($request->validated(), [
            'program' => $user->program,
            'student_id' => $user->student_id,
        ]);

        //check if user already applies for mentorship in the same community
        $mentor = Mentor::with('user','community')
        ->where('user_id',$user->id)
                ->where('community_id',$request->community_id)
                ->first();
        if($mentor){
            return response()->json(
                [
                    'message' => 'You have already applied for mentorship in this community',
                ],
                400
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
        $applications = Mentor::with('user','community')->get();

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

        if($mentor->status= 'approved' && $mentor->user->type == "mentor"){
            return response()->json([
                'message' => 'User is already a mentor of this community'
            ], 400);
        }

        Mail::to($mentor->user->email)->send(new MentorshipApplicationStatus($mentor, 'approved', $mentor->user));
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
        $mentors = $community->mentor()->where('status', 'approved')->with('user')->get();

        return response()->json([
            'data' => $mentors
        ]);
    }

    public function rejectApplication(Mentor $mentor){


        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'error' => 'You are not authorized to reject this application'
            ], 403);
        }
        $mentor->update([
            'status' => 'rejected']);
        Mail::to($mentor->user->email)->send(new MentorshipApplicationStatus($mentor,'rejected', $mentor->user));
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
        $mentor->user->update([
            'type' => 'mentor'
        ]);
        //delete the mentor in the table
        $mentor->delete();
        return response()->json([
            'message' => 'Mentorship revoked successfully'
        ]);
    }

    public function retireMentorship(Mentor $mentor, Request $request)
    {
     //make sure that the user is the owner of the mentorship
        if (auth()->user()->id !== $mentor->user_id || auth()->user()->type !== 'admin') {
            return response()->json([
                'message' => 'You are not the owner of this mentorship'
            ], 403);
        }

        //have mentor select which community they want to retire from
        $request->validate([
            'community_id' => 'required|exists:communities,id'
        ]);

        if($mentor->community_id !== $request->community_id){
            return response()->json([
                'message' => 'You are not a mentor of this community'
            ], 403);
        }


        $mentor->delete();
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
