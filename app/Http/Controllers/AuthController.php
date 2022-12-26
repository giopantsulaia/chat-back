<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Jobs\SendEmail;
use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        User::create($request->validated());

        return response()->json(['message'=>'Registered Successfully.'], 202);
    }

    public function verify(Request $request): JsonResponse
    {
        $user = User::findOrFail((int)substr($request->hash,0,2));

        if(!hash_equals((string) substr($request->hash,2),hash('sha256',$user->email))) {
            throw new AuthorizationException();
        }
        $user->markEmailAsVerified();

        return response()->json(['message'=>'Email verified.'], 200);
    }
}
