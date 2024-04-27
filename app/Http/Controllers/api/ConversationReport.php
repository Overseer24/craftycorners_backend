<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Message\ReportedConversation;
use App\Http\Resources\Message\ReportedConversations;
use App\Models\User;
use App\Notifications\ReporterResolveConversation;
use App\Notifications\ReportResolvedNotification;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\ReportConversation;

class ConversationReport extends Controller
{
    public function reportConversation(Request $request, Conversation $conversation)
    {
        $request->validate([
            'reason' => 'required|string',
            'description' => 'required|string',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = auth()->user()->id;

        $reported_user = $conversation->sender_id == $user ? $conversation->receiver_id : $conversation->sender_id;

        $proofFilename = null;

        if ($request->hasFile('proof')) {
            $proof = $request->file('proof');
            $proofFilename = time() . '.' . $proof->getClientOriginalExtension();
            $proof->storeAs('public/reports/conversation'.$conversation->id.'/proof', $proofFilename);
        }

        $conversation->reports()->create([
            'user_id' => $user,
            'reported_user_id' => $reported_user,
            'reason' => $request->reason,
            'description' => $request->description,
            'proof' => $proofFilename,
        ]);

        return response()->json([
            'message' => 'Conversation reported successfully'
        ]);
    }

    public function resolveReport(Request $request, Conversation $conversation)
    {
        $request->validate([
            'resolution_description' => 'nullable|string',
            'resolution_option'=> 'required',
        ]);

        $report = $conversation->reports()->where('id', $request->report_id)->first();

        if (!$report || $report->is_resolved) {
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

        if ($report->resolution_option === 'warn') {
            $reportedUser->notify(new ReportResolvedNotification($resolutionOption, null));
            $report->user->notify(new ReporterResolveConversation($report));
        } elseif ($report->resolution_option === 'suspend') {
            //update reported user type to suspended
            $reportedUser->update(['type' => 'suspended']);
            $report->update(['unsuspend_date' => $unsuspendDate]);
            $reportedUser->notify(new ReportResolvedNotification($resolutionOption, $unsuspendDate));
            $report->user->notify(new ReporterResolveConversation($report));
        }
        return response()->json([
            'message' => 'Report resolved successfully'
        ]);
    }


    public function showReport($conversation, $reportId)
    {
        $report = ReportConversation::with(['user', 'conversation'])->where('id', $reportId)->where('conversation_id', $conversation)->first();

        if(!$report){
            return response()->json([
                'message' => 'Report not found'
            ], 404);
        }

        return new ReportedConversations($report);
    }

    public function showAllReports($conversation)
    {
        $reports = ReportConversation::with(['user', 'conversation'])->where('conversation_id', $conversation)->get();

        return ReportedConversation::collection($reports);
    }

}
