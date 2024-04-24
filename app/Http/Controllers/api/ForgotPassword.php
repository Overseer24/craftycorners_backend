<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Password;
class ForgotPassword extends Controller
{
    public function sendResetLinkEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => 'required|email']);
       $user = User::where('email', $request->email)->first();
        if ($user) {
            $token = Password::getRepository()->create($user);
            $user->ResetPassword($token);
            $status = Password::RESET_LINK_SENT;
        }else{
            $status = Password::INVALID_USER;
        }

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['email' => __($status)], 400);
    }

    public function resetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required','confirmed',
                PasswordRule::min(8)
                ->mixedCase()
                ->letters()
                ->symbols()
                ->numbers()
            ],[
                'password.mixed_case' => 'The password must contain at least one uppercase and one lowercase letter.',
                'password.letters' => 'The password must contain at least one letter.',
                'password.symbols' => 'The password must contain at least one symbol.',
                'password.numbers' => 'The password must contain at least one number.',
            ]
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
