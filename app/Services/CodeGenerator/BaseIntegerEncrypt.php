<?php

namespace App\Services\CodeGenerator;

use App\Interfaces\CodeGenerator\IntegerEncryptInterface;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BaseIntegerEncrypt
	implements IntegerEncryptInterface
{
	private readonly int $maxBitNumberLength;
	private readonly int $symbolsCount;

	public function __construct(
		private readonly int     $exponent,
		private readonly int     $prime,
		private readonly array   $symbols,
		private readonly array   $bitPermutation,
		private readonly ?string $codePattern = null
	) {
		$this->symbolsCount       = count($symbols);
		$this->maxBitNumberLength = count($bitPermutation);
	}

	/**
	 * Shuffle bits of number
	 */
	private function shuffleBits(int $num): int
	{
		$result = 0;

		for ($i = 0; $i < $this->maxBitNumberLength; $i++) {
			// Берем i-й бит исходного числа
			$bit = ($num >> $i) & 1;
			// Ставим его на новую позицию
			if ($bit) {
				$result |= (1 << $this->bitPermutation[$i]);
			}
		}

		return $result;
	}

	/**
	 * Revert shuffled bits
	 */
	private function unshuffleBits(int $num): int
	{
		$result = 0;
		for ($i = 0; $i < $this->maxBitNumberLength; $i++) {
			// Берем бит с перемешанной позиции
			$bit = ($num >> $this->bitPermutation[$i]) & 1;
			// Ставим его на исходную позицию
			if ($bit) {
				$result |= (1 << $i);
			}
		}
		return $result;
	}

	/**
	 * Additional shuffling using prime-modulo multiplication
	 * This is reversible when using the inverse element modulo
	 */
	public function shuffleMod(int $num): int
	{
		return (int)bcmod(bcmul($num, self::MULTIPLIER), $this->prime);
	}

	public function unshuffleMod(int $num): int
	{
		// Находим обратный элемент мультипликативно
		return (int)bcmod(bcmul($num, $this->modInverse(self::MULTIPLIER, $this->prime)), $this->prime);
	}

	/**
	 * Finding the inverse element modulo (Euclid's extended algorithm)
	 */
	public function modInverse(int $a, int $m): ?int
	{
		if ($this->gcd($a, $m) !== 1) {
			return null; // Inverse doesn't exist
		}

		$m0 = $m;
		$x0 = 0;
		$x1 = 1;

		while ($a > 1) {
			$q = intval($a / $m);
			$t = $m;

			$m = $a % $m;
			$a = $t;
			$t = $x0;

			$x0 = $x1 - $q * $x0;
			$x1 = $t;
		}

		if ($x1 < 0) {
			$x1 += $m0;
		}

		return $x1;
	}

	/**
	 * Calculate Greatest Common Divisor
	 *
	 * @param int $a  First number
	 * @param int $b  Second number
	 *
	 * @return int GCD
	 */
	private function gcd(int $a, int $b): int
	{
		while ($b !== 0) {
			$temp = $b;
			$b    = $a % $b;
			$a    = $temp;
		}
		return $a;
	}

	/**
	 * Converts a number to a base string
	 */
	public function toBase(int $num): string
	{
		if ($num === 0) {
			return str_repeat($this->symbols[0], $this->exponent);
		}

		$result = Str::of('');
		$temp   = $num;
		while ($temp > 0) {
			$result = $result->prepend($this->symbols[$temp % $this->symbolsCount]);
			$temp   = intdiv($temp, $this->symbolsCount);
		}

		return str_pad($result, $this->exponent, $this->symbols[0], STR_PAD_LEFT);
	}

	/**
	 * Converts the base string back to a number
	 *
	 * @param string $str
	 *
	 * @return int
	 */
	public function fromBase(string $str): int
	{
		$num = 0;
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++) {
			$pos = strpos(implode('', $this->symbols), $str[$i]);
			if ($pos === false) {
				throw new InvalidArgumentException("Недопустимый символ: {$str[$i]}");
			}
			$num = $num * $this->symbolsCount + $pos;
		}
		return $num;
	}

	protected function stringToPattern(string $string, ?string $pattern = null): string
	{
		$pattern ??= $this->codePattern;
		if (is_null($pattern) || Str::of($pattern)->isEmpty()) {
			return $string;
		}

		$string = collect(mb_str_split($string));

		return collect(mb_str_split($pattern))
			->reverse()
			->map(
				function (string $item) use ($string) {
					if ($item !== self::CODE_PATTERN_SYMBOL || $string->isEmpty()) {
						return $item;
					}

					return $string->pop();
				}
			)
			->reverse()
			->join('');
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function stringFromPattern(string $stringCollection, ?string $patternCollection = null): string
	{
		$patternCollection      = collect(mb_str_split($patternCollection ?? $this->codePattern));
		$stringCollection       = collect(mb_str_split($stringCollection));
		$returnStringCollection = $stringCollection;

		if ($patternCollection->isNotEmpty()) {
			if ($patternCollection->isNotEmpty() && $patternCollection->count() !== $stringCollection->count()) {
				throw new InvalidArgumentException('Неверный формат кода!', 400);
			}

			$returnStringCollection =
				$stringCollection->intersectByKeys(
					$patternCollection->filter(fn (string $item) => $item === self::CODE_PATTERN_SYMBOL)
				);
		}

		return $returnStringCollection->join('');
	}

	/**
	 * Code generation by ID with full shuffling
	 */
	public function generate(?int $id, ?string $pattern = null): ?string
	{
		if (is_null($id)) {
			return null;
		}

		$maxId = pow($this->symbolsCount, $this->exponent) - 1;

		if ($id > $maxId) {
			throw new InvalidArgumentException("ID {$id} превышает максимальное значение {$maxId}");
		}

		// Применяем два слоя перемешивания
		$shuffled = $this->shuffleBits($id);
		$shuffled = $this->shuffleMod($shuffled);

		return $this->stringToPattern($this->toBase($shuffled), $pattern);
	}

	/**
	 * Get ID from code
	 */
	public function extract(?string $code, ?string $pattern = null): int
	{
		$cleanCode = $this->stringFromPattern($code, $pattern);

		if (strlen($cleanCode) !== $this->exponent) {
			throw new InvalidArgumentException('Неверный формат кода!', 400);
		}

		$shuffled = $this->fromBase($cleanCode);
		$shuffled = $this->unshuffleMod($shuffled);

		// Обратное преобразование в обратном порядке
		return $this->unshuffleBits($shuffled);
	}
}