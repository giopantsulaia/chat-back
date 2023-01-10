<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Notifications\NewEmailVerification;
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

		if (!Auth::attempt($validated))
		{
			return response()->json(['message' => 'Incorrect credentials.'], 401);
		}

		if (!$user->hasVerifiedEmail())
		{
			return response()->json(['message' => 'Email not verified!'], 403);
		}

		return response()->json(['token' => $user->createToken('api token')->plainTextToken, 'expires_at' => time() + 3600]);
	}

	public function logout(): JsonResponse
	{
		try
		{
			auth('sanctum')->user()->currentAccessToken()->delete();

			return response()->json(['message' => 'Logged out.']);
		}
		catch (\Exception $e)
		{
			return response()->json(['message' => $e->getMessage()], 500);
		}
	}

	public function verify(Request $request): JsonResponse
	{
		if ($request->email)
		{
			$user = User::firstWhere('verification_code', $request->token);
			$user->update(['email'=>$request->email, 'email_verified_at' => now(), 'verification_code' => null]);

			return response()->json(['message' => 'New email activated']);
		}
		else
		{
			$user = User::findOrFail((int)strtok($request->hash, '|'));
			$correctUser = hash_equals((string) substr($request->hash, strpos($request->hash, '|') + 1), hash('sha256', $user->email));

			if (!$correctUser)
			{
				throw new AuthorizationException();
			}
			$user->markEmailAsVerified();

			return response()->json(['message'=>'Email verified.'], 200);
		}
	}

	public function show(): JsonResponse
	{
		return response()->json(['user' => auth()->user()]);
	}

	public function update(Request $request): JsonResponse
	{
		$user = auth('sanctum')->user();

		if ($request->avatar)
		{
			$file = $request->file('avatar');
			$file_name = time() . '.' . $file->getClientOriginalName();
			$file->move(public_path('storage/avatars'), $file_name);
			$user->avatar = 'storage/avatars/' . $file_name;
			$user->save();
		}

		if ($request->email)
		{
			if (User::firstWhere('email', $request->email))
			{
				return response()->json(['message'=>'Email exists']);
			}

			$user->verification_code = sha1($user->id . $request->email . time());
			$user->save();
			$user->notify(new NewEmailVerification($user->verification_code, $request->email));
		}

		if ($request->first_name)
		{
			$user->update(['first_name' => $request->validated()['first_name']]);
		}
		if ($request->last_name)
		{
			$user->update(['last_name' => $request->validated()['last_name']]);
		}
		if ($request->phone)
		{
			$user->update(['phone' => $request->validated()['phone']]);
		}
		if ($request->birth_date)
		{
			$user->update(['birth_date' => $request->validated()['birth_date']]);
		}
		if ($request->gender)
		{
			$user->update(['gender' => $request->validated()['gender']]);
		}

		return response()->json(['message' => 'User updated successfully.']);
	}
}
