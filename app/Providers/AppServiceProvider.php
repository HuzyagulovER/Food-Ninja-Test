<?php

namespace App\Providers;

use App\Macros\BlueprintMacros;
use App\Services\CodeGeneratorFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider
	extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		$this->app->singleton(
			'code-generator',
			function () {
				return CodeGeneratorFactory::factory(
					7,
					'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
				);
			}
		);
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		Blueprint::mixin(new BlueprintMacros);
	}
}
