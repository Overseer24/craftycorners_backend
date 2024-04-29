<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Report\ReportedConversations;
use App\Http\Resources\Report\ReportedPosts;
use App\Http\Resources\Report\ReportedComments;
use App\Http\Resources\Report\ShowSpecificReport;
use App\Mail\ReportResolved;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Report;
use App\Models\ReportPost;
use App\Models\User;
use App\Notifications\ReporterResolve;
use App\Notifications\ReportNotification;
use App\Notifications\ReportResolvedNotification;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Stmt\Return_;

class ReportController extends Controller
{
    public function report(Request $request, $type, $id){
        $request->validate([
            'reason' => 'required|string',
            'description' => 'nullable|string',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'

        ]);

        $userId = auth()->user()->id;
        $reportedUserId = null;
        $reportableType = null;

        switch ($type){
            case 'post':
                $reportableType = 'App\Models\Post';
                $post = Post::find($id);
                if(!$post){
                    return response()->json([
                        'message' => 'Post not found'
                    ], 404);
                }
                $reportedUserId = $post->user_id;
                break;

            case 'comment':
                $reportableType = 'App\Models\Comment';
                $comment = Comment::find($id);
                if(!$comment){
                    return response()->json([
                        'message' => 'Comment not found'
                    ], 404);
                }
                $reportedUserId = $comment->user_id;
                break;
            case 'conversation':
                $reportableType = 'App\Models\Conversation';
                $conversation = Conversation::find($id);
                if(!$conversation){
                    return response()->json([
                        'message' => 'Conversation not found'
                    ], 404);
                }
                $reportedUserId = $conversation->sender_id == $userId ? $conversation->receiver_id : $conversation->sender_id;
                break;
            default:
                return response()->json([
                    'message' => 'Invalid reportable type'
                ], 400);
        }

        $proofFileName = null;

        if($request->hasFile('proof')){
            $proof = $request->file('proof');
            $proofFileName = time().'_'.$proof->getClientOriginalName();
            $proof->storeAs('reports/proofs', $proofFileName, 'public');
        }

      Report::create([
            'user_id' => $userId,
            'reported_user_id' => $reportedUserId,
            'reportable_type' => $reportableType,
            'reportable_id' => $id,
            'reason' => $request->reason,
            'description' => $request->description,
            'proof' => $proofFileName
        ]);

      return response()->json([
          'message' => 'Report submitted successfully'
      ]);
    }

    public function resolveReport(Request $request, $type, $id){

        $request->validate([
            'resolution_description' => 'required|string',
            'resolution_option'=> 'required|string|in:warn,suspend,ignore',
        ],
        ['resolution_option' => 'Resolution option must be either warn or suspend']);

        $report = Report::where('reportable_type', 'App\Models\\'.ucfirst($type))->where('reportable_id', $id)->where('is_resolved', false)->first();
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
        $reportedUser = User::find($report->reported_user_id);
        $report->update([
            'is_resolved' => true,
            'resolved_by' => auth()->user()->id,
            'resolved_at' => now(),
            'resolution_description' => $request->resolution_description,
            'resolution_option' => $resolutionOption,
        ]);
        if ($report->resolution_option== 'ignore'){
            $report->user->notify(new ReporterResolve($report));
        }
        if ($report->resolution_option === 'warn') {
            $reportedUser->notify(new ReportResolvedNotification($resolutionOption, null));
            $report->user->notify(new ReporterResolve($report));
        } elseif ($report->resolution_option === 'suspend') {
            //update poster type to suspended
            $reportedUser->update(['type' => 'suspended']);
            $report->update(['unsuspend_date' => $unsuspendDate]);
            $reportedUser->notify(new ReportResolvedNotification($resolutionOption, $unsuspendDate));
            $report->user->notify(new ReporterResolve($report));
            //delete reported post
            $report->reportable->delete();
        }

        //send mail to the user who reported the post
//         Mail::to($report->user->email)->send(new ReportResolved($report));
        //notify reporter

        return response()->json([
            'message' => 'Report resolved successfully'
        ]);
    }

//    public function showPostReports()
//    {
//        $reports = Report::where('reportable_type', 'App\Models\Post')->with(['reportable' => function ($query) {
//            $query->withTrashed();
//        }])->get();
//
//        return response()->json([
//            'data' => ReportedPosts::collection($reports)
//        ]);
//    }
//
//
//    public function showCommentReports()
//    {
//        $reports = Report::where('reportable_type', 'App\Models\Comment')->with('reportable','reportedUser','user')
//            ->get();
//
//        return response()->json([
//            'data' => ReportedComments::collection($reports)
//        ]);
//
//    }
//
//    public function showConversationReports()
//    {
//        $reports = Report::where('reportable_type', 'App\Models\Conversation')->with('reportable','reportedUser','user')->get();
//
//        return response()->json([
//            'data' => ReportedConversations::collection($reports)
//        ]);
//
//    }

    public function showPostReports()
    {
        $reports = Report::where('reportable_type', 'App\Models\Post')
            ->with([
                'reportable' => function ($query) {
                    $query->withTrashed();
                },
                'reportedUser' => function ($query) {
                    $query->withTrashed();
                },
                'user' => function ($query) {
                    $query->withTrashed();
                }
            ])->get();

        return response()->json([
            'data' => ReportedPosts::collection($reports)
        ]);
    }

    public function showCommentReports()
    {
        $reports = Report::where('reportable_type', 'App\Models\Comment')
            ->with([
                'reportable' => function ($query) {
                    $query->withTrashed();
                },
                'reportedUser' => function ($query) {
                    $query->withTrashed();
                },
                'user' => function ($query) {
                    $query->withTrashed();
                }
            ])->get();

        return response()->json([
            'data' => ReportedComments::collection($reports)
        ]);
    }

    public function showConversationReports()
    {
        $reports = Report::where('reportable_type', 'App\Models\Conversation')
            ->with([
                'reportable' => function ($query) {
                    $query->withTrashed();
                },
                'reportedUser' => function ($query) {
                    $query->withTrashed();
                },
                'user' => function ($query) {
                    $query->withTrashed();
                }
            ])->get();

        return response()->json([
            'data' => ReportedConversations::collection($reports)
        ]);
    }


    public function showSpecificReport($id)
    {
        $report = Report::with ([ 'user', 'reportedUser', 'reportable' => function ($query) {
            $query->withTrashed();
        }])->find($id);

        if(!$report){
            return response()->json([
                'message' => 'Report not found'
            ], 404);
        }

        return new ShowSpecificReport($report);
    }
}
