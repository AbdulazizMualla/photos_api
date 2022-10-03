<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index');
    }

    public function index(): AnonymousResourceCollection
    {
        $posts = Post::with('comments' , 'user')->paginate(100);
        return PostResource::collection($posts);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'post_title' => 'required',
            'post_body' => 'required',
        ]);

        try {
            $user = auth()->user();
            $createPost = $user->posts()->create($validateData);
            return response()->json(['message' => new PostResource($createPost->load('user.profile'))]);
        } catch (\Exception $exception){
            return response()->json($exception , 500);
        }

    }

    public function update(Request $request , Post $post)
    {
        $validateData = $request->validate([
            'post_title' => 'string',
            'post_body' => 'string',
        ]);
        try {
            if ($post->user_id != auth()->id()) throw new \Exception('You don\'t this resource' , 401);
            $post->update($validateData);
            return response()->json(['message' => 'post updated successfully']);
        }catch (\Exception $exception){
            return response()->json($exception , 500);
        }
    }

    public function destroy(Post $post)
    {
        try {
            if ($post->user_id != auth()->id()) throw new \Exception('You don\'t this resource' , 401);
            $post->delete();
            return response()->json(['message' => 'post deleted successfully']);
        } catch (\Exception $exception){
            return response()->json($exception , 500);
        }
    }

    public function myPosts(): JsonResponse
    {
        try {
            $myPosts = Post::where('user_id' , auth()->id())->get();
            return response()->json(PostResource::collection($myPosts->load('user.profile')));
        } catch (\Exception $exception){
            return response()->json($exception , 500);
        }
    }

    public function forceDelete($postId): JsonResponse
    {
        try {
            $post = Post::withTrashed()->find($postId);
            if ($post->user_id != auth()->id()) throw new \Exception('You don\'t this resource' , 401);
            $post->forceDelete();
            return response()->json(['message' => 'post force deleted successfully']);
        } catch (\Exception $exception){
            return response()->json($exception , 500);
        }
    }

}
