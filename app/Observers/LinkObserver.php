<?php

namespace App\Observers;

use App\Models\Link;

class LinkObserver
{
	/**
	 * Handle the Link "deleted" event.
	 */
	public function deleted(Link $link): void
	{
		$link->redirects()->delete();
	}
}
