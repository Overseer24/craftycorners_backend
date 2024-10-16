<?php

use App\Http\Controllers\api\ConversationReport;
use App\Http\Controllers\api\FileController;
use App\Http\Controllers\api\ForgotPassword;
use App\Http\Controllers\api\MentorController;
use App\Http\Controllers\api\MessageController;
use App\Http\Controllers\api\NotificationController;
use App\Http\Controllers\api\ReportController;
use App\Http\Controllers\api\SearchController;
//use App\Http\Controllers\api\UpdateProfile;
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

use Illuminate\Http\Response;





Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::post('/send-email-verification', [VerificationController::class, 'sendEmailVerification'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

Route::get('/verify-email/{id}/{hash}', [VerificationController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');

Route::post('/forgot-password', [ForgotPassword::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');

Route::post('/reset-password', [ForgotPassword::class, 'resetPassword'])->middleware('guest')->name('password.reset');

Route::post('/resend-verification-email', [VerificationController::class, 'resendVerificationEmail'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.resend');


Route::middleware(['auth:sanctum','verified','ensureUserNotSuspended'])
    ->group(function () {

        Route::get('/files/{conversation}/{file}',[FileController::class,'ConversationFiles']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [UserController::class,'me']);
        Route::get('/user-levels', [UserController::class, 'getUserLevels']);

        Route::get('/user-levels/{user}',[UserController::class, 'specificUserLevels']);


        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

        Route::apiResource('/users', UserController::class);
        Route::get('/deactivated-users', [UserController::class, 'showDeactivatedUsers']);
        Route::post('/deactivate/{user}', [UserController::class, 'deactivateUser']);
        Route::post('/activate/{user}', [UserController::class, 'reactivateUser']);
        Route::post('/done-assessment', [UserController::class, 'doneAssessment']);


//        //Post to Specific Joined Communities
//        Route::post('/post-community/{community}', [PostController::class, 'postInCommunity']);

        Route::post('/change-password', [AuthController::class, 'authChangePassword']);
        Route::post('/change-email', [AuthController::class, 'changeEmail']);

        Route::apiResource('/schedule', ScheduleController::class);

        Route::get('/show-schedule/{user}', [ScheduleController::class, 'showUserSchedules']);
        Route::post('/schedule-recurring', [ScheduleController::class, 'storeRecurring']);

        Route::apiResource('/articles', ArticleController::class);
        Route::get('joined/articles', [ArticleController::class, 'showArticlesByJoinedCommunity']);
        Route::apiResource('/videos', VideoController::class);
        Route::get('joined/videos', [VideoController::class, 'showVideosByJoinedCommunity']);


//        Route::apiResource('/user-community', UserCommunityController::class);

        //join leave community
        Route::post('/join-community/{community}', [UserCommunityController::class, 'joinCommunity']);
        Route::post('/leave-community/{community}', [UserCommunityController::class, 'leaveCommunity']);

        //like unlike post
        Route::post('/like-post/{post}/', [PostController::class, 'like']);
        Route::post('/unlike-post/{post}/', [PostController::class, 'unlike']);

        //share post
        Route::post('/share-post/{post}', [PostController::class, 'share']);

        //fetch all auth users post on their homepage base on the community they joined to
        Route::get('/homepage-post', [PostController::class, 'showHomepagePost']);
        //fetch specific post of user
        Route::get('/user/{user}/posts', [UserController::class,'showUserPost']);
        //fetch all post by filtered subtopics
        Route::get('subtopic/{community}/posts', [PostController::class, 'showPostBySubtopic']);
        //recommend communities
        Route::get('/recommend-communities', [CommunityController::class, 'recommendCommunities']);

        Route::apiResource('communities', CommunityController::class);
        //show all users joined communities
        Route::get('/user-joined-communities', [CommunityController::class, 'showUserJoinedCommunities']);
        //show all subtopics of a community
        Route::get('/community/{community}/subtopics', [CommunityController::class, 'showCommunitySubtopics']);
        //show communities by subtopic
        Route::get('/subtopic/communities', [CommunityController::class, 'getCommunitiesBySubtopics']);
        //add subtopics to community
        Route::post('/community/{community}/subtopic', [CommunityController::class, 'addCommunitySubtopic']);
        //delete subtopic of a community
        Route::delete('/community/{community}/subtopic', [CommunityController::class, 'deleteCommunitySubtopic']);

        //show all list of communities
        Route::get('/list/communities', [CommunityController::class, 'showListCommunities']);

        //fetch all post of a specific user
        Route::apiResource('/posts', PostController::class)->middleware(['negativeWordFilter']);
        //show all deleted post
        Route::get('deleted/posts', [PostController::class, 'showDeletedPosts']);
        //show specific deleted post
        Route::get('deleted/post/{id}', [PostController::class, 'showDeletedPost']);
        //show all delete post in community
        Route::get('deleted/posts/{community}', [PostController::class, 'showDeletedPostOnCommunity']);
        //permanently delete post
        Route::delete('/posts/permanently-delete/{post}', [PostController::class, 'permanentDelete']);
        //fetch all posts by community
        Route::get('/communities/{community}/posts', [PostController::class, 'showPostByCommunity']);
        //Use this route to only view all comments and delete the comments also update the comments
        Route::apiResource('/comments', CommentController::class)->except(['index']);
        //fetch all comments by post
        Route::get('/post/{postId}/comments', [CommentController::class, 'showCommentByPost']);
        //add comment to post
        Route::post('/post/{post}/comment', [CommentController::class, 'store'])->middleware(['negativeWordFilter', 'throttle:6,1']);


        //show all mentors
//        Route::get('/mentors',[MentorController::class, 'showAllMentors']);
        //get auth user mentor
        Route::get('/mentor', [MentorController::class, 'showAuthMentor']);
        //shows specific user mentor
        Route::get('/mentor/{user}', [MentorController::class, 'getUserMentor']);
        //show list of community of approved mentor
        Route::get('/mentor-communities', [MentorController::class, 'showAuthUserMentorCommunities']);
        //show all approved mentors
        Route::get('/approved-mentors',[MentorController::class, 'showApprovedMentors']);
        //apply for mentorship
        Route::post('/apply-for-mentorship', [MentorController::class, 'applyForMentorship']);
        Route::get('/mentorship-applications', [MentorController::class, 'viewApplications']);
        Route::get('/mentorship-application/{mentor}', [MentorController::class, 'showApplication'])    ;
        Route::post('/accept-mentorship-application/{mentor}', [MentorController::class, 'approveApplication']);
        Route::post('/reject-mentorship-application/{mentor}', [MentorController::class, 'rejectApplication']);
        Route::get('/show-mentors-of-community/{community}', [MentorController::class, 'showMentorsOfCommunity']);
        Route::post('/mentor/{mentor}/set-assessment_date', [MentorController::class, 'setAssessmentDate']);
        Route::post('/mentor/{mentor}/cancel-application', [MentorController::class, 'cancelApplication']);

        Route::post('/mentor/{mentor}/revoke-mentorship', [MentorController::class, 'revokeMentorship']);
        Route::post('/mentor/retire-mentorship/{community}', [MentorController::class, 'retireMentorship']);

        Route::post('/like-mentor/{mentor}', [MentorController::class, 'likeMentor']);
        Route::post('/unlike-mentor/{mentor}', [MentorController::class, 'unlikeMentor']);

        #report
        Route::get('/report/posts', [ReportController::class, 'showPostReports']);
        Route::get('/report/comments', [ReportController::class, 'showCommentReports']);
        Route::get('/report/conversations', [ReportController::class, 'showConversationReports']);
        Route::get('/report/{id}', [ReportController::class, 'showSpecificReport']);
        Route::post('/report/{type}/{id}', [ReportController::class, 'report']);
        Route::post('/resolve-report/{type}/{id}', [ReportController::class, 'resolveReport']);


        #CONVERSATION
        //search conversation
        Route::get('/search-conversation', [SearchController::class, 'searchConversation']);
        //whenever user start a conversation but the content is empty and leaves the conversation delete them
        Route::delete('/conversation/{conversation_id}', [MessageController::class, 'deleteEmptyConversation']);
        //start conversation
        Route::post('/start-a-conversation/{receiver_id}', [MessageController::class, 'startAConversation']);
        //send message
        Route::post('/conversation/message/{receiver_id}',[MessageController::class, 'sendMessage']);
        //when user open specific conversation
        Route::get('/conversation/message/{receiver_id}', [MessageController::class, 'getConversation']);
        //list all user conversation
        Route::get('/conversations', [MessageController::class, 'getConversations']);
        //mark as read
        Route::post('/conversation/mark-as-read/{conversation_id}', [MessageController::class, 'markAsRead']);
        //delete message
        Route::delete('/conversation/delete-message/{message_id}', [MessageController::class, 'deleteMessage']);

        //search
        Route::get('/search', [SearchController::class, 'index']);

    });//end of auth middleware

//try to print hello world on route /
Route::get('/', function () {
    return response()->json(['message' => 'Hello World!'], Response::HTTP_OK);
});



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');


//Route::get('/encrypt-all-messages', [MessageController::class, 'encryptAllMessages']);



