<?php

namespace App\Http\Controllers;

use App\Http\Requests\FriendRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class FriendController extends Controller
{
	public function send(FriendRequest $request): JsonResponse
	{
		$friendsTo = User::find($request->friend_id);

		auth('sanctum')->user()->friendsTo()->attach($friendsTo);

		return response()->json(['message'=> 'Friend request sent successfully.']);
	}

	public function destroy(FriendRequest $request): JsonResponse
	{
		$friend = User::find($request->friend_id);

		try
		{
			$user = auth('sanctum')->user();

			$user->friendsTo()->detach($friend);

			$user->friendsFrom()->detach($friend);

			return response()->json(['message'=> 'Friend removed successfully.']);
		}
		catch(\Exception $e)
		{
			return response()->json(['message' => $e->getMessage()]);
		}
	}

	public function accept(FriendRequest $request): JsonResponse
	{
		$requestFrom = $request->user_id;

		auth('sanctum')->user()->friendsPendingFrom()->firstWhere('user_id', $requestFrom)->pivot->update(['accepted' => 1]);

		return response()->json(['message'=> 'Friend removed successfully.']);
	}
}
