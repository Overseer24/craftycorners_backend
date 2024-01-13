<?php

use App\Http\Controllers\api\UpdateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\CommunityController;
use App\Http\Controllers\api\PostController;
use App\Http\Controllers\api\CommentController;
use App\Http\Controllers\api\ScheduleController;
use App\Http\Controllers\api\ArticleController;
use App\Http\Controllers\api\VideoController;
use App\Http\Controllers\api\UserCommunityController;
use App\Http\Controllers\api\VerificationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route::post('/send-email-verification', function () {
//     request()->user()->sendEmailVerificationNotification();
//     return response()->json(['message' => 'Email verification link sent']);
// })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Route::get('/verify-email/{id}/{hash}', function (Request $request, $id, $hash) {
//     $user = \App\Models\User::find($id);

//     if (!$user || ! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
//         return response()->json(['message' => 'Invalid verification link'], 400);
//     }

//     if ($user->hasVerifiedEmail()) {
//         return view('email-verification-success',['message' => 'Email already verified']);
        // return response()->json(['message' => 'Email already verified']);
//     }

//     if ($user->markEmailAsVerified()) {
//         event(new \Illuminate\Auth\Events\Verified($request->user()));
//     }

//     return response()->json(['message' => 'Email verified']);
// })->middleware(['signed'])->name('verification.verify');


Route::post('/send-email-verification', [VerificationController::class, 'sendEmailVerification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/verify-email/{id}/{hash}', [VerificationController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');

Route::middleware('auth:sanctum',)
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request
                ->user();
        });
        Route::apiResource('/users', UserController::class);
        Route::apiResource('communities', CommunityController::class);
        Route::apiResource('/posts', PostController::class);

        Route::post('/post/{post}/comment', [CommentController::class, 'store']);

        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/change-email', [AuthController::class, 'changeEmail']);
        Route::apiResource('/schedule', ScheduleController::class);

        Route::apiResource('/articles', ArticleController::class);
        Route::apiResource('/videos', VideoController::class);

        // Route::apiResource('/user-community', UserCommunityController::class);

        Route::post('/join-community/{community}', [UserCommunityController::class, 'joinCommunity']);
        Route::post('/leave-community/{community}', [UserCommunityController::class, 'leaveCommunity']);

        Route::post('/like-post/{post}/', [PostController::class, 'like']);
        Route::post('/unlike-post/{post}/', [PostController::class, 'unlike']);



    });

// Route::middleware('verified')
//     ->group(function () {

//     });

Route::get('/communities/{communityId}/users', [UserCommunityController::class, 'showCommunityMembers']);

Route::get('/communities', [CommunityController::class, 'index']);
Route::get('/communities/{id}', [CommunityController::class, 'show']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

Route::get('/comments', [CommentController::class, 'index']);
Route::get('/comments/{id}', [CommentController::class, 'show']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
