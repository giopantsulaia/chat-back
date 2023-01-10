<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'first_name'  => ['min:2', 'max:16', 'alpha_dash'],
			'last_name'   => ['min:2', 'max:32', 'alpha_dash'],
			'email'       => ['email', 'unique:users,email'],
			'birth_date'  => ['date'],
			'gender'      => ['nullable', 'string', 'in:male,female,other'],
			'phone'       => ['nullable'],
            'avatar' => ['image']
		];
	}
}
