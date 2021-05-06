<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        $validateData['password'] = bcrypt($validateData['password']);

        $user = User::create($validateData);
        if ($user){
            $accessToken = $user->createToken('authToken')->accessToken;
            if ($accessToken){
                return ['user' =>  new UserResource($user) , 'access_token' => $accessToken];
            }
            return response()->json(['message' => 'Error pleas try aging'] , 500);
        }
        return response()->json(['message' => 'Error pleas try aging'] , 500);
    }

    public function login(Request $request)
    {
        $validateData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!auth()->attempt($validateData)){
            return response()->json(['message' => 'invalid login details'] , 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        if ($accessToken){
            return ['user' =>  new UserResource(auth()->user()->load('profile')) , 'access_token' => $accessToken];
        }

        return response()->json(['message' => 'Error pleas try aging'] , 500);
    }
}
