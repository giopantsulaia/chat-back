<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
	public function show(User $user): JsonResponse
	{
		$friendship = null;
		$pending = auth('sanctum')->user()->friendsPendingTo()->where('friend_id', $user->id)->first();
		$isFriend = auth('sanctum')->user()->friends()->where('id', $user->id)->first();

		if ($pending)
		{
			$friendship = 'pending';
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

		return response()->json(['users' => $users], 200);
	}
}
