<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index');
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'comment_body' => 'required|string',
            'post_id' => 'required|exists:posts,id'
        ]);
        try {
            $user = auth()->user();
            $comment = $user->comments()->create($validateData);
            return response()->json(['message' => new CommentResource($comment->load('user.profile'))]);
        } catch (\Exception $exception){
            return response()->json($exception , 500);
        }
    }
}
