<?php

namespace App\Services;

use App\Interfaces\CodeGenerator\IntegerEncryptInterface;
use App\Services\CodeGenerator\IntegerEncrypt;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CodeGeneratorFactory
{
	public const ALPHABET           = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // 33 символа
	public const PRIMES_BY_EXPONENT = [
		1  => 1_091,
		2  => 35_951,
		3  => 1_185_929,
		4  => 39_135_401,
		5  => 1_291_468_007,
		6  => 42_618_442_987,
		7  => 1_406_408_618_249,
		8  => 46_411_484_401_981,
		9  => 1_531_578_985_264_483,
		10 => 50_542_106_513_726_827,
		11 => 1_667_889_514_952_985_179,
	];

	// Переставляем биты так, чтобы соседние числа давали совершенно разные коды
	public const BIT_PERMUTATION = [
		63, 61, 59, 57, 55, 53, 51, 49, 47, 45, 43, 41, 39, 37, 35, 33, 31, 29, 27, 25, 23, 21, 19, 17, 15, 13, 11, 9,
		7, 5, 3, 1,  // нечетные в обратном порядке
		62, 60, 58, 56, 54, 52, 50, 48, 46, 44, 42, 40, 38, 36, 34, 32, 30, 28, 26, 24, 22, 20, 18, 16, 14, 12, 10, 8,
		6, 4, 2, 0,   // четные в обратном порядке
	];

	public const DEFAULT_EXPONENT = 7;

	/**
	 * @throws Exception
	 */
	public static function factory(
		int|string   $exponent = self::DEFAULT_EXPONENT,
		string|array $symbols = self::ALPHABET
	): IntegerEncryptInterface {
		if (is_string($symbols)) {
			$symbols = collect(mb_str_split($symbols))->toArray();
		}

		if (is_array($symbols)) {
			$collect = collect($symbols);

			if ($collect->isEmpty()
			    || $collect->duplicates()->isNotEmpty()
			    || !$collect->every(fn (string|int $item) => Str::of($item)->length() === 1)
			) {
				throw new Exception('Invalid symbols!');
			}

			$symbols = $collect->sort()->values()->toArray();
		}

		$maxBitNumberLength = strlen(decbin(pow(count($symbols), $exponent))) - 1;

		return new IntegerEncrypt(
			$exponent,
			self::PRIMES_BY_EXPONENT[$exponent],
			$symbols,
			self::getBitPermutation($maxBitNumberLength)
		);
	}

	private static function getBitPermutation(int $symbolsCount): array
	{
		return collect(self::BIT_PERMUTATION)->filter(fn (int $item) => $item < $symbolsCount)->values()->toArray();
	}

	private static function getCodePattern(int $symbolsCount): string
	{
		return Collection::times($symbolsCount, fn () => IntegerEncryptInterface::CODE_PATTERN_SYMBOL)
		                 ->chunk(round($symbolsCount / 2))
		                 ->reverse()
		                 ->map(fn (Collection $c) => $c->implode(''))
		                 ->implode('-');
	}
}