<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
	public function show(User $user): JsonResponse
	{
		return response()->json(['user' => $user], 200);
	}

	public function search(Request $request): JsonResponse
	{
		$users = User::where('first_name', 'like', '%' . $request->search . '%')->orWhere('last_name', 'like', '%' . $request->search . '%')->get();

		return response()->json(['users' => $users], 200);
	}
}
