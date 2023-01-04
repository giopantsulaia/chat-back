<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('first_name', 24);
			$table->string('last_name', 48);
			$table->string('email')->unique();
			$table->string('birth_date')->nullable();
			$table->string('gender')->nullable();
			$table->string('phone')->nullable();
			$table->string('avatar')->nullable();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password');
			$table->string('verification_code')->nullable();
			$table->rememberToken();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users');
	}
};
