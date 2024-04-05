<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\ReportedPost;
use App\Mail\ReportResolved;
use App\Models\ReportPost;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Mail;

class ReportController extends Controller
{
    public function reportPost(Request $request, Post $post){
        $request->validate([
            'reason' => 'required|string',
            'description' => 'required|string'
        ]);

        if($post->reports()->where('user_id', auth()->user()->id)->exists()){
            return response()->json([
                'message' => 'You have already reported this post'
            ], 400);
        }

        $post->reports()->create([
            'user_id' => auth()->user()->id,
            'post_id' => $post->id,
            'reason' => $request->reason,
            'description' => $request->description,
        ]);


        return response()->json([
            'message' => 'Post reported successfully'
        ]);
    }

    public function resolveReport(Request $request, Post $post){
        $request->validate([
            'resolution_description' => 'required|string'
        ]);

        $report = $post->reports()->where('id', $request->report_id)->first();

        if(!$report){
            return response()->json([
                'message' => 'Report not found'
            ], 404);
        }

        $report->update([
            'is_resolved' => true,
            'resolved_by' => auth()->user()->id,
            'resolved_at' => now(),
            'resolution_description' => $request->resolution_description
        ]);

        //send mail to the user who reported the post
         Mail::to($report->user->email)->send(new ReportResolved($report));

        return response()->json([
            'message' => 'Report resolved successfully'
        ]);
    }

    public function showReports(Post $post){
        //eager load the reports with the user
        $reports = $post->reports()->with('user')->get();
        return New ReportedPost($reports);
    }



    public function showReport(Post $post, $reportId){
        $report = $post->reports()->where('id', $reportId)->with('user')->first();

        if(!$report){
            return response()->json([
                'message' => 'Report not found'
            ], 404);
        }

        return new ReportedPost($report);
    }

    public function showAllReports(){
        $reports = ReportPost::with('user', 'post')->get();

        return response()->json($reports);
    }
}
