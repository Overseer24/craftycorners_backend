<?php

namespace App\Http\Controllers\api;

use App\Events\PostComment;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $comments = Comment::with('user')->get();
        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Post $post) {
        $comment = new Comment();
        $comment->user_id = auth()->user()->id;
        $comment->post_id = $post->id;
        $comment->content = request('content');
        $comment->save();

        $commenter = auth()->user(); // Get the user who commented

        if ($post->notifiable && $post->user_id !== $commenter->id) {
            $post->user->notifyUser('post_comment', $comment, $commenter->id);
            broadcast(new PostComment(new CommentResource($comment)))->toOthers();
        }


        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => new CommentResource($comment)
        ]);
    }

    /**
     * Display the specified resource.
     */
    //display specific comment
    public function show(Comment $comment) {
           return new CommentResource($comment);
    }

    // Display the ALl Comments related to post
    public function showCommentByPost(Post $postId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $comments = $postId->comments()->get();

        return CommentResource::collection($comments);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, Comment $comment) {

        $comment->update($request->validated());
        return new CommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
