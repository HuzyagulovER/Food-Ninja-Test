<?php

namespace App\Interfaces\CodeGenerator;

interface IntegerEncryptInterface
{
	const MULTIPLIER          = 65537;
	const CODE_PATTERN_SYMBOL = '_';

	public function generate(?int $id): ?string;

	public function extract(?string $code): ?int;
}