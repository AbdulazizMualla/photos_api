<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index');
    }

    public function index()
    {
        $photos = Photo::with('user.profile')->paginate(1);
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

    }

    public function destroy(Photo $photo)
    {

    }
}
