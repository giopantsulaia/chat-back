<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Staudenmeir\LaravelMergedRelations\Eloquent\HasMergedRelationships;

class User extends Authenticatable implements MustVerifyEmail
{
	use HasApiTokens;

	use HasFactory;

	use Notifiable;

	use HasMergedRelationships;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'password',
		'birth_date',
		'phone',
		'avatar',
		'gender',
		'verification_code',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	protected $appends = [
		'number_of_friends',
	];

	public function numberOfFriends(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->friends()->count()
		);
	}

	public function setPasswordAttribute($password): void
	{
		$this->attributes['password'] = bcrypt($password);
	}

	public function friendsTo(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
			->withPivot('accepted')
			->withTimestamps();
	}

	public function friendsFrom(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
			->withPivot('accepted')
			->withTimestamps();
	}

	public function friendsPendingTo()
	{
		return $this->friendsTo()->wherePivot('accepted', false);
	}

	public function friendsPendingFrom()
	{
		return $this->friendsFrom()->wherePivot('accepted', false);
	}

	public function friendsAcceptedTo()
	{
		return $this->friendsTo()->wherePivot('accepted', true);
	}

	public function friendsAcceptedFrom()
	{
		return $this->friendsFrom()->wherePivot('accepted', true);
	}

	public function friends()
	{
		return $this->mergedRelationWithModel(User::class, 'friends_view');
	}
}
