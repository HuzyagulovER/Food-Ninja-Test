<?php

namespace App\Services;

use App\Interfaces\MakeableInterface;
use App\Models\Link;
use App\Models\Redirect as RedirectModel;
use App\Traits\Makeable;
use Illuminate\Http\RedirectResponse;

class Redirect
	implements MakeableInterface
{
	use Makeable;

	protected Symlink $symlink;

	public function __construct(protected string $code)
	{
		$this->initServices();
	}

	protected function initServices(): void
	{
		$this->symlink = new Symlink($this->code);
	}

	public function redirect(): RedirectResponse
	{
		return \Illuminate\Support\Facades\Redirect::to($this->getRedirectLink());
	}

	public function getRedirectLink(): string
	{
		return $this->getLink()->link;
	}

	public function getLink(): Link
	{
		return $this->symlink->decode();
	}

	public function recordRedirect(): self
	{
		RedirectModel::create(
			[
				'link_id'    => $this->getLink()->id,
				'ip_address' => request()->ip(),
			]
		);
		return $this;
	}
}