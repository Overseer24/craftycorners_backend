<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
        $reports = $post->reports()->with('user')->get();

        return response()->json([
           'data'=>[
               'id' => $this->id,
               'user' => [
                   'id' => $this->user->id,
                   'first_name' => $this->user->first_name,
                   'middle_name' =>$this->user->middle_name,
                   'last_name' => $this->user->last_name,
                   'user_name' => $this->user->user_name,
                   'profile_picture' => $this->user->profile_picture,
                   'type' => $this->user->type,
               ],
               'title' => $this->title,
               'content' => $this->content,
               'image' => $this->image,
               'video' => $this->video,
               'link' => $this->link,
               'post_type' => $this->post_type,
               'created_at' => $this->created_at->format('Y-m-d H:i:s'),
               'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

               'community' => [
                   'id' => $this->community->id,
                   'name' => $this->community->name,
                   'description' => $this->community->description,
                   'image' => $this->community->image,
                   'members_count' => $this->community->members_count,
               ],
               'likes_count'=> $this->likes_count,
               'comments_count'=> $this->comments_count,
               'shares' => $this->shares,
           ]
        ]);
    }



    public function showReport(Post $post, $reportId){
        $report = $post->reports()->where('id', $reportId)->with('user')->first();

        if(!$report){
            return response()->json([
                'message' => 'Report not found'
            ], 404);
        }

        return response()->json($report);
    }

    public function showAllReports(){
        $reports = ReportPost::with('user', 'post')->get();

        return response()->json($reports);
    }
}
