<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function ConversationFiles(Conversation $conversation ,$file)
    {

        //check if user is part of the conversation
        if(!auth()->user()->conversations->contains($conversation)){
            return response()->json(['message' => 'You are not part of this conversation'], 403);
        }

        return Storage::disk('private')->response("messages/conversation_{$conversation->id}/attachments/{$file}");

    }
}
