<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
	public function show(User $user): JsonResponse
	{
		$authUser = auth('sanctum')->user();

		$friendship = null;
		$pending = $authUser->friendsPendingTo()->firstWhere('friend_id', $user->id);
		$isFriend = $authUser->friends()->firstWhere('id', $user->id);
		$incoming = $authUser->friendsPendingFrom()->firstWhere('user_id', $user->id);

		if ($pending)
		{
			$friendship = 'pending';
		}
		elseif ($incoming)
		{
			$friendship = 'incoming';
		}
		elseif ($isFriend)
		{
			$friendship = 'friends';
		}

		return response()->json(['user' => $user, 'friend' => $friendship], 200);
	}

	public function search(Request $request): JsonResponse
	{
		$users = User::where('first_name', 'like', '%' . $request->search . '%')->orWhere('last_name', 'like', '%' . $request->search . '%')->get();

		return response()->json(['users' => UserResource::collection($users)], 200);
	}
}
