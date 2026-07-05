<?php

namespace App\Macros;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;

class BlueprintMacros
{
	public function foreignIdForConstrained(): callable
	{
		return function (
			string  $model,
			?string $column = null,
			string  $comment = '',
			bool    $isNullable = true,
			string  $after = null
		) {
			/** @var Blueprint $this */
			return $this->foreignIdFor($model, $column)
			            ->nullable($isNullable)
			            ->comment($comment)
			            ->when(
				            !empty($after),
				            fn (ColumnDefinition $definition) => $definition->after($after)
			            )
			            ->constrained();
		};
	}

	public function userId(): callable
	{
		return function (?string $column = null, string $comment = 'ID пользователя', bool $isNullable = true) {
			/** @var Blueprint $this */
			return $this->foreignIdForConstrained(User::class, $column, $comment, $isNullable);
		};
	}
}