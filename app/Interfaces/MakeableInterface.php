<?php

namespace App\Interfaces;

interface MakeableInterface
{
	static function make(): static;
}