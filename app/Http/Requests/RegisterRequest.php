<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'first_name'            => ['required', 'min:2', 'max:16', 'alpha_dash'],
			'last_name'             => ['required', 'min:2', 'max:32'],
			'email'                 => ['required', 'email', 'unique:users,email'],
			'password'              => ['required', 'min:6', 'max:255', 'confirmed'],
			'password_confirmation' => ['required'],
		];
	}
}
