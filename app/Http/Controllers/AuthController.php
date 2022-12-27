<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        User::create($request->validated());

        return response()->json(['message'=>'Registered Successfully.'], 202);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->firstOrFail();

        if (!Auth::attempt($validated)) {

            return response()->json(['message' => 'Incorrect credentials.'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified!'], 403);
        }

        return response()->json(['token' => $user->createToken('api token')->plainTextToken,'expires_at' => time() + 3600]);

    }

    public function logout(): JsonResponse
    {
        try {
            auth('sanctum')->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $request): JsonResponse
    {
        $user = User::findOrFail((int)strtok($request->hash,'|'));

        if(!hash_equals((string) substr($request->hash, strpos($request->hash, "|") + 1),hash('sha256',$user->email))) {
            throw new AuthorizationException();
        }
        $user->markEmailAsVerified();

        return response()->json(['message'=>'Email verified.'], 200);
    }

    public function show(): JsonResponse
    {
        return response()->json(['user' => auth()->user()]);
    }
}
