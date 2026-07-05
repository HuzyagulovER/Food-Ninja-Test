<?php

namespace App\Http\Controllers;

use App\Services\Redirect as RedirectService;

class Redirect
	extends Controller
{
	public function show(string $code)
	{
		return RedirectService::make($code)
		                      ->recordRedirect()
		                      ->redirect();
	}
}
