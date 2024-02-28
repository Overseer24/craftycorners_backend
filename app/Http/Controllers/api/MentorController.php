<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\MentorApplicationRequest;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MentorController extends Controller
{
    public function applyForMentorship(MentorApplicationRequest $request){
        $user = auth()->user()->mentor()->create($request->validated());

        return response()->json([
            'message' => 'Mentorship application submitted successfully',
            'data' => $user
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

    public function approveApplication(Mentor $mentor){
        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'message' => 'You are not authorized to approve this application'
            ], 403);
        }
        $mentor->update([
            'status' => 'approved'
        ]);
        $mentor->user->update([
            'type' => 'mentor'
        ]);
        return response()->json([
            'message' => 'Application approved successfully'
        ]);
    }

    public function rejectApplication(Mentor $mentor){
        if(!auth()->user()->type == 'admin'){
            return response()->json([
                'message' => 'You are not authorized to reject this application'
            ], 403);
        }
        $mentor->update([
            'status' => 'rejected'
        ]);
        return response()->json([
            'message' => 'Application rejected successfully'
        ]);
    }
}
