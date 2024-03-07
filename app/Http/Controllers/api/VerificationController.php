<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    public function sendEmailVerification(Request $request)
    {
        if($request->user()->hasVerifiedEmail()){
            return response()->json([
                'message' => 'Email already verified'
            ], 400);
        }

        $request->user()->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Email verification link sent on your email id'
        ]);
    }


    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email Verified']);

    }


}

