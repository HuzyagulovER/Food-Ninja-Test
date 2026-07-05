<?php

namespace App\Services;

use App\Models\Link;

class Symlink
{
	protected const SYMLINK_PATH = '/l/';
	protected string $symlink;

	public function __construct(?string $symlink = null)
	{
		if (!is_null($symlink)) {
			$this->setSymlink($symlink);
		}
	}

	public function setSymlink(string $symlink): self
	{
		$this->symlink = $symlink;
		return $this;
	}

	public static function encode(Link $link): string
	{
		return static::getPreUrl() . app('code-generator')->generate($link->id);
	}

	public function decode(): Link
	{
		return Link::find(app('code-generator')->extract($this->extractCodeFromSymlink()));
	}

	public static function getPreUrl(): string
	{
		return config('app.url') . self::SYMLINK_PATH;
	}

	protected function extractCodeFromSymlink(): string
	{
		return substr(str_replace(self::getPreUrl(), '', url()->current()), 0);
	}
}