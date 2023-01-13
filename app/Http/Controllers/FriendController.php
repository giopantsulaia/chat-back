<?php

namespace App\Http\Controllers;

use App\Events\NewFriendRequest;
use App\Http\Requests\FriendRequest;
use App\Http\Resources\UserResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
	public function send(FriendRequest $request): JsonResponse
	{
		DB::transaction(function () use ($request) {
			$friendsTo = User::find($request->friend_id);
			auth('sanctum')->user()->friendsTo()->attach($friendsTo);

			$notification = Notification::create(['user_id' => auth('sanctum')->id(), 'recipient_id' => $request->friend_id, 'type' => 'friend']);

			event(new NewFriendRequest($notification));
		});

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

	public function getMyFriends(): JsonResponse
	{
		$user = auth('sanctum')->user();

		$friends = $user->friends;

		return response()->json(['friends' => UserResource::collection($friends)]);
	}

	public function getUserFriends(Request $request): JsonResponse
	{
		$user = User::find($request->id);

		$friends = $user->friends;

		return response()->json(['friends' => UserResource::collection($friends)]);
	}
}
