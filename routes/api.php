<?php

use App\Http\Controllers\api\MentorController;
use App\Http\Controllers\api\MessageController;
use App\Http\Controllers\api\ReportController;
use App\Http\Controllers\api\UpdateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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
use App\Events\NewMessage;
use Illuminate\Http\Response;



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



Route::middleware(['auth:sanctum','negativeWordFilter'])
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request
                ->user();
        });


        Route::apiResource('/users', UserController::class);
        Route::apiResource('communities', CommunityController::class);

//        //Post to Specific Joined Communities
//        Route::post('/post-community/{community}', [PostController::class, 'postInCommunity']);

        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/change-email', [AuthController::class, 'changeEmail']);

        Route::apiResource('/schedule', ScheduleController::class);

        Route::get('/show-schedule/{user}', [ScheduleController::class, 'showUserSchedules']);

        Route::apiResource('/articles', ArticleController::class);

        Route::apiResource('/videos', VideoController::class);



//        Route::apiResource('/user-community', UserCommunityController::class);

        //join leave community
        Route::post('/join-community/{community}', [UserCommunityController::class, 'joinCommunity']);
        Route::post('/leave-community/{community}', [UserCommunityController::class, 'leaveCommunity']);

        //like unlike post
        Route::post('/like-post/{post}/', [PostController::class, 'like']);
        Route::post('/unlike-post/{post}/', [PostController::class, 'unlike']);

        //fetch all auth users post on their homepage base on the community they joined to
        Route::get('/homepage-post', [PostController::class, 'showHomepagePost']);
        //fetch specific post of user
        Route::get('/user/{user}/posts', [UserController::class,'showUserPost']);

        //fetch all post of a specific user
        Route::apiResource('/posts', PostController::class);
        //fetch all posts by community
        Route::get('/communities/{community}/posts', [PostController::class, 'showPostByCommunity']);
        //Use this route to only view all comments and delete the comments also update the comments
        Route::apiResource('/comments', CommentController::class)->except(['index']);
        //fetch all comments by post
        Route::get('/post/{postId}/comments', [CommentController::class, 'showCommentByPost']);
        //add comment to post
        Route::post('/post/{post}/comment', [CommentController::class, 'store']);


        Route::post('/apply-for-mentorship/', [MentorController::class, 'applyForMentorship']);
        Route::get('/mentorship-applications/', [MentorController::class, 'viewApplications']);
        Route::get('/mentorship-application/{mentor}', [MentorController::class, 'showApplication'])    ;
        Route::post('/accept-mentorship-application/{mentor}', [MentorController::class, 'approveApplication']);
        Route::post('/reject-mentorship-application/{mentor}', [MentorController::class, 'rejectApplication']);
        Route::get('/show-mentors-of-community/{community}', [MentorController::class, 'showMentorsOfCommunity']);
        Route::post('/mentor/{mentor}/set-assessment_date', [MentorController::class, 'setAssessmentDate']);
        Route::post('/mentor/{mentor}/cancel-application', [MentorController::class, 'cancelApplication']);

        Route::post('/message/send', [MessageController::class, 'sendMessage']);
        Route::get('/message/{receiver_id}', [MessageController::class, 'getMessages']);

        Route::get('/show-all-reports', [ReportController::class, 'showAllReports']);
        Route::post('/report-post/{post}', [ReportController::class, 'reportPost']);
        Route::get('/show-reports/{post}', [ReportController::class, 'showReports']);
        Route::get('/show-report/{post}/{reportId}', [ReportController::class, 'showReport']);
        Route::post('/resolve-report/{post}', [ReportController::class, 'resolveReport']);

        Route::post('/chat/send/{receiver_id}',[MessageController::class, 'sendMessage']);

        Route::get('/chat/messages/{receiver_id}', [MessageController::class, 'getMessages']);

    });//end of auth middleware


// Route::middleware('verified')
//     ->group(function () {

//     });

Route::get('/communities/{communityId}/users', [UserCommunityController::class, 'showCommunityMembers']);

Route::get('/communities', [CommunityController::class, 'index']);
Route::get('/communities/{id}', [CommunityController::class, 'show']);

//Route::get('/posts/', [PostController::class, 'index']);
//Route::get('/posts/', [PostController::class, 'show']);
//Route::get('/posts/{community}', [PostController::class, 'show']);

Route::get('/comments', [CommentController::class, 'index']);
//Route::get('/comments/{comment}', [CommentController::class, 'show']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

