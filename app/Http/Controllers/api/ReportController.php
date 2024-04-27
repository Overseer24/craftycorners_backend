<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\ReportedPost;
use App\Http\Resources\Post\ReportedPosts;
use App\Mail\ReportResolved;
use App\Models\ReportPost;
use App\Models\User;
use App\Notifications\ReporterResolvePost;
use App\Notifications\ReportNotification;
use App\Notifications\ReportResolvedNotification;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Mail;

class ReportController extends Controller
{
    public function reportPost(Request $request, Post $post){
        $request->validate([
            'reason' => 'required|string',
            'description' => 'required|string',
        ]);

        if($post->reports()->where('user_id', auth()->user()->id)->exists()){
            return response()->json([
                'message' => 'You have already reported this post'
            ], 400);
        }
        $post->reports()->create([
            'user_id' => auth()->user()->id,
            'reported_user_id'=>$post->user->id,
            'post_id' => $post->id,
            'reason' => $request->reason,
            'description' => $request->description,
        ]);
//        $report = $post->reports()->create([
//            'user_id' => auth()->user()->id,
//            'post_id' => $post->id,
//            'reason' => $request->reason,
//            'description' => $request->description,
//        ]);

        //notify all admins about the report
//        $admins = User::where('type', 'admin')->get();
//        foreach ($admins as $admin){
//            $admin->notify(new ReportNotification($report, $post));
//        }


        return response()->json([
            'message' => 'Post reported successfully'
        ]);
    }

    public function resolveReport(Request $request, Post $post){
        $request->validate([
            'resolution_description' => 'nullable|string',
             'resolution_option'=> 'required'
        ]);

        $report = $post->reports()->where('id', $request->report_id)->first();

        if(!$report || $report->is_resolved){
            return response()->json([
                'message' => 'Report already resolved or not found'
            ], 404);
        }

        $resolutionOption = $request->resolution_option;
        $unsuspendDate = null;

        if ($resolutionOption==='suspend'){
            $request->validate([
                'unsuspend_date' => 'required|date|after:today'
            ]);

            $unsuspendDate = $request->unsuspend_date;
        }
        $reportedUser = $post->user;
        $report->update([
            'is_resolved' => true,
            'resolved_by' => auth()->user()->id,
            'resolved_at' => now(),
            'resolution_description' => $request->resolution_description,
            'resolution_option' => $resolutionOption,
        ]);

        if ($report->resolution_option === 'warn') {
            $reportedUser->notify(new ReportResolvedNotification($resolutionOption, null));
            $report->user->notify(new ReporterResolvePost($report));
        } elseif ($report->resolution_option === 'suspend') {
            //update poster type to suspended
            $reportedUser->update(['type' => 'suspended']);
            $report->update(['unsuspend_date' => $unsuspendDate]);
            $reportedUser->notify(new ReportResolvedNotification($resolutionOption, $unsuspendDate));
            $report->user->notify(new ReporterResolvePost($report));
            //delete reported post
            $post->delete();
        }

        //send mail to the user who reported the post
//         Mail::to($report->user->email)->send(new ReportResolved($report));
        //notify reporter



        return response()->json([
            'message' => 'Report resolved successfully'
        ]);
    }


    public function showReport($post, $reportId){
       $report = ReportPost::with(['user', 'post' => function ($query) {
            $query->withTrashed();
        }])->where('id', $reportId)->where('post_id', $post)->first();

        if(!$report){
            return response()->json([
                'message' => 'Report not found'
            ], 404);
        }

        return new ReportedPost($report);
    }

    public function showAllReports(){
        $reports = ReportPost::with(['user', 'post' => function ($query) {
            $query->withTrashed();
        }])->get();


        return response()->json([
            'data' => ReportedPosts::collection($reports)
        ]);
    }
}
