<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\UserController;
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

Route::controller(AuthController::class)->group(function () {
	Route::post('/register', 'register')->name('register');
	Route::post('/login', 'login')->name('login');
	Route::post('/logout', 'logout')->name('logout')->middleware('auth:sanctum');
	Route::post('/verify-email', 'verify')->name('email.verify');
	Route::get('/me', 'show')->name('get.authenticated_user_data')->middleware('auth:sanctum', 'verified');
	Route::put('/user', 'update')->name('update.user_data')->middleware('auth:sanctum');
});

Route::controller(UserController::class)->group(function () {
	Route::get('/users/{user}', 'show')->name('get.user')->middleware('auth:sanctum', 'verified');
	Route::post('/search', 'search')->name('search.users')->middleware('auth:sanctum', 'verified');
});

Route::controller(FriendController::class)->group(function () {
	Route::post('/friend-request', 'send')->name('send.friend_request')->middleware('auth:sanctum', 'verified');
	Route::post('/remove-friend', 'destroy')->name('remove.friend')->middleware('auth:sanctum', 'verified');
	Route::post('/accept-friend', 'accept')->name('accept.friend')->middleware('auth:sanctum', 'verified');
});
