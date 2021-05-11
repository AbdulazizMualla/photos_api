<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\UserResource;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index');
    }

    public function index()
    {
        $photos = Photo::with('user.profile')->paginate(100);
        return PhotoResource::collection($photos);

    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'file' => 'required|max:5000|mimes:jpg,jpeg,png',
            'title' => 'required'
        ]);

        $user = auth()->user();
        $fileName = $request->file('file')->hashName();
        $fileUpload = $request->file('file')->storeAs('public/photos/'.$user->id , $fileName);
        if ($fileUpload){
            $request['photo_name'] = $fileName;
            $createPhoto = $user->photos()->create($request->all());
            if ($createPhoto){
                return response()->json(['message' => new PhotoResource($createPhoto->load('user.profile'))]);
            }
            return response()->json(['message' => 'Error pleas try aging']  , 500);
        }
        return response()->json(['message' => 'Error pleas try aging']  , 500);

    }

    public function myPhotos()
    {
        $user = auth()->user();
        $user['profile'] = $user->profile;
        return response()->json(['user' => new UserResource($user->load('photos'))]);

    }

    public function destroy(Photo $photo)
    {
        if (auth()->id() != $photo->user_id){
            return response()->json(['message' => 'You don\'t have one this resource'] , 401);
        }
        if ($photo->delete()){
            return response()->json(['message' => 'resource deleted']);
        }
        return response()->json(['message' => 'Error, try aging later'] , 500);

    }


    public function forceDelete($photoId)
    {

        $photo = Photo::withTrashed()->findOrFail($photoId);

        if (auth()->id() != $photo->user_id){
            return response()->json(['message' => 'You don\'t this resource'] , 401);
        }
        if ($photo->forceDelete()){
            Storage::delete('public/photos/'.$photo->user_id.'/'.$photo->photo_name);
            return response()->json(['message' => 'Force deleted resource successfully']);
        }
        return response()->json(['message' => 'Error , try aging later'] , 500);
    }

    public function deletedPhotos()
    {
        $user = auth()->user();
        return response()->json(['user' => new UserResource($user->load(
                ['photos' => function($query){
                        $query->onlyTrashed();
                    }, 'profile'
                ]
             ))
        ]);
    }
}
