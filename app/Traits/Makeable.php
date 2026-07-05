<?php

namespace App\Traits;

trait Makeable
{
	public static function make(): static
	{
		return new static(...func_get_args());
	}
}