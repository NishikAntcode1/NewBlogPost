<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAllComments']]);
    }

    public function createComment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|min:2|max:200',
            'blog_id' => 'required|exists:blogs,id',
            'parent_id' => 'exists:comments,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        // return $user;
        $comment = Comment::create([
            'body' => $request->body,
            'blog_id' => $request->blog_id,
            'user_id' => $user->id,
            'parent_id' => $request->parent_id
        ]);

        return response()->json([
            'message' => 'Comment created sucessfully!',
            'comment' => $comment
        ], 201);
    }

    public function getAllComments($blogId)
    {
        $blog = Blog::find($blogId);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $comments = Comment::where('blog_id', $blogId)
            ->whereNull('parent_id')
            ->with('replies') // Eager load replies relationship
            ->with('user')
            ->get();

        // This function recursively fetches the replies for each comment
        $fetchReplies = function ($comments) use (&$fetchReplies) {
            foreach ($comments as $comment) {
                $comment->replies;
                $fetchReplies($comment->replies);
            }
        };

        $fetchReplies($comments);

        return response()->json(['comments' => $comments]);
    }
}
