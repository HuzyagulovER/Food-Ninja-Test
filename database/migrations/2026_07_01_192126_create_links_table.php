<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class
	extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create(
			'links',
			function (Blueprint $table) {
				$table->id();
				$table->userId();
				$table->string('title', 64)->comment('Title');
				$table->text('link')->comment('Link');

				$table->timestamps();

				$table->index('user_id');

				$table->comment('Links table');
			}
		);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('links');
	}
};
