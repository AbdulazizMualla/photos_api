<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/register' , 'App\Http\Controllers\Api\AuthController@register');
Route::post('/login' , 'App\Http\Controllers\Api\AuthController@login');
Route::post('/profile' , 'App\Http\Controllers\Api\ProfileController@store');

Route::get('/photos' , 'App\Http\Controllers\Api\PhotoController@index');

Route::post('/photos' , 'App\Http\Controllers\Api\PhotoController@store');

Route::get('/my-photos' , 'App\Http\Controllers\Api\PhotoController@myPhotos');

Route::delete('/photos/{photo}' , 'App\Http\Controllers\Api\PhotoController@destroy');
Route::delete('/photos/{photo}/force-delete' , 'App\Http\Controllers\Api\PhotoController@forceDelete');
Route::get('/deleted-photos' , 'App\Http\Controllers\Api\PhotoController@deletedPhotos');

Route::resource('posts' , 'App\Http\Controllers\Api\PostController');
Route::get('/my-posts' , [PostController::class , 'myPosts']);

