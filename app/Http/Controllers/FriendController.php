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
		$friendsTo = User::find($request->friend_id);

		auth('sanctum')->user()->friendsTo()->detach($friendsTo);

		return response()->json(['message'=> 'Friend removed successfully.']);
	}
}
