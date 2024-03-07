<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Password;

class ForgotPassword extends Controller
{
    public function sendResetLinkEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['email' => __($status)], 400);
    }

    public function resetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => bcrypt($request->password)
                ])->save();
            }
        );
        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => __($status)])
            : response()->json(['email' => __($status)], 400);
    }

}
