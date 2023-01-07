<?php

namespace App\Http\Controllers;

use App\Http\Requests\FriendRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class FriendController extends Controller
{
	public function send(FriendRequest $request): JsonResponse
	{
		$friendsTo = User::find($request->friends_to);

		auth('sanctum')->user()->friendsTo()->attach($friendsTo);

		return response()->json(['message'=> 'Friend request sent successfully.']);
	}
}
