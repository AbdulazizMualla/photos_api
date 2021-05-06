<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
   public function __construct()
   {
       $this->middleware('auth:api');
   }


   public function store(Request $request)
   {
       $user = auth()->user();

       $userUpdate = $user->update($request->only('name' , 'email'));
       if (!$userUpdate){
           return response()->json(['message' => 'Error pleas try aging'] , 500);
       }

       $profile = $user->profile;

       if (!$request->description && $request->file('file') == null){
           if ($profile && !$profile->file_name){
               $user->profile()->delete();
           }
           return ['user' =>  new UserResource($user->load('profile'))];
       }


       if ($request->description && $request->file('file') == null){
           if ($profile){
               $user->profile()->update($request->only('description'));
               return ['user' =>  new UserResource($user->load('profile'))];
           }else{
               $user->profile()->create($request->only('description'));
               return ['user' =>  new UserResource($user->load('profile'))];
           }
       }


        $v = Validator::make($request->all() , [
             'file' => 'required|max:5000|mimes:jpg,jpeg,png'
        ]);
        if ($v->fails()){
            return response()->json($v->errors() , 422);
        }

       Storage::deleteDirectory('public/users/'.$user->id);
       $fileName = $request->file('file')->hashName();
       $request['file_name'] = $fileName;

       $uploadFile =  $request->file('file')->storeAs('public/users/'.$user->id , $fileName);
       if ($uploadFile){
           if ($profile){
              $updateProfile = $user->profile()->update($request->except('name' , 'email' , 'file'));
              if ($updateProfile){
                  return ['user' =>  new UserResource($user->load('profile'))];
              }
           }
           $crateProfile = $user->profile()->create($request->except('name' , 'email' , 'file'));
           if ($crateProfile){
               return ['user' =>  new UserResource($user->load('profile'))];
           }
       }


   }
}
