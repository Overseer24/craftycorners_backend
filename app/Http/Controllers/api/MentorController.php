<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\MentorApplicationRequest;
use App\Mail\MentorshipApplicationStatus;
use App\Models\Community;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MentorController extends Controller
{
    public function applyForMentorship(MentorApplicationRequest $request){

        $user = auth()->user();
        //check if user already applies for mentorship in the same community
        $mentor = Mentor::where('user_id',$user->id)
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

        $mentor = $user->mentor()->create($request->validated());


        return response()->json([
            'message' => 'Mentorship application submitted successfully',
            'data' => $mentor
        ], 201);
    }

    public function viewApplications(){

        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'message' => 'You are not authorized to view this page'
            ], 403);
        }
        $applications = Mentor::with('user','community')->get();
        return response()->json([
            'data' => $applications
        ]);
    }

    public function showApplication(Mentor $mentor){
        if(!auth()->user()->type== 'admin'){
            return response()->json([
                'message' => 'You are not authorized to view this page'
            ], 403);
    }
        return response()->json([
            'data' => $mentor,
            'user' => $mentor->user,
        ]);
    }

    public function approveApplication(Mentor $mentor){

        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'message' => 'You are not authorized to approve this application'
            ], 403);
        }
        $mentor->update([
            'status' => 'approved'
        ]);
        if($mentor->status= 'approved'){
            return response()->json([
                'message' => 'User is already a mentor of this community'
            ], 400);
        }

        Mail::to($mentor->user->email)->send(new MentorshipApplicationStatus($mentor, 'approved', $mentor->user));
        $mentor->user->update([
            'type' => 'mentor'
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
                'message' => 'You are not authorized to reject this application'
            ], 403);
        }
        $mentor->update([
            'status' => 'rejected']);
        Mail::to($mentor->user->email)->send(new MentorshipApplicationStatus($mentor, 'rejected'));
        //send email or live notification to the user

        return response()->json([
            'message' => 'Application rejected successfully'
        ]);
    }
}
