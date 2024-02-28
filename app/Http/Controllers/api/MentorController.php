<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\MentorApplicationRequest;
use App\Models\Mentor;
use Illuminate\Http\Request;

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

//    public function ReviewApplication(Request $request, Mentor $mentor){
//        if(!auth()->user()->type == 'admin'){
//            return response()->json([
//                'message' => 'You are not authorized to view this page'
//            ], 403);
//        }
//        $mentor->update($request->all());
//        return response()->json([
//            'message' => 'Application reviewed successfully'
//        ]);
//    }
}
