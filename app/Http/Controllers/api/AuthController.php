<?php

namespace App\Http\Controllers\api;


use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserDetails\ChangeEmail;
use App\Http\Requests\UserDetails\ChangePassword;
use App\Http\Requests\UserDetails\ResetPasswordRequest;
use App\Http\Requests\UserDetails\SendResetLinkRequest;
use Illuminate\Support\Facades\Password;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Mail\ResetPasswordMail;
use Illuminate\Auth\Events\Registered;
use App\Notifications\VerifyEmail;



class AuthController extends Controller
{
    //  use EmailVerification;
    //Register
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'user_name' => $request->user_name,
            'birthday' => $request->birthday,
            'password' => bcrypt($request->password),
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'student_id' => $request->student_id,
            'program' => $request->program,
        ]);

        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture');
            $fileName = $user->id . '.' . now()->format('YmdHis'). '.' . $profile_picture->getClientOriginalExtension();
            $profile_picture->storeAs('public/users', $fileName);
            $user->profile_picture = $fileName;
            $user->save();
        }

        $user->notify(new VerifyEmail);
        //Send Email Verification
        // event(new Registered($user));


        $token = $user->createToken('UserToken')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    // public function registered(Request $request, $user)
    // {
    //     event(new Registered($user));
    //     Mail::to($user->email)->send(new VerifyEmail($user));
    //     return response()->json([
    //         'message' => 'User registered successfully'
    //     ]);
    // }

    //Login
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 422);

        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->type === 'suspended') {
            $unsuspendDate = $user->reportedPosts()->where('resolution_option', 'suspend')->first()->unsuspend_date;

            return response()->json([
                'message' => 'Your account is suspended until ' . $unsuspendDate
            ], 403);
        }


        $token = $user->createToken('UserToken')->plainTextToken;
        $responseData = [
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'roles' => $user->type
        ];
        if ($user->type == 'admin') {
            $totalUsers = User::count();
            $responseData['permissions'] = [
                'create' => true,
                'read' => true,
                'update' => true,
                'delete' => true

            ];
            $responseData['totalUsers'] = $totalUsers;
        }

        return response()->json($responseData);
    }
    //Auth Change Password
    public function authChangePassword(ChangePassword $request)
    {
        $user = auth()->user();

        $validatedData = $request->validated();

        if (!password_verify($validatedData['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid old password'
            ], 422);
        }

        $user->update([
            'password' => bcrypt($validatedData['new_password'])
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);

    }
    //Auth Change Email
    public function authChangeEmail(ChangeEmail $request)
    {
        $user = auth()->user();

        $validatedData = $request->validated();

        $user->update([
            'email' => $validatedData['email']
        ]);

        return response()->json([
            'message' => 'Email changed successfully'
        ]);
    }

    //On Login Reset
    //
    //
    //
    //
    //On Logout
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}
