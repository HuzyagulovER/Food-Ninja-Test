<?php

namespace App\Filament\Resources\RedirectResource\Pages;

use App\Filament\Resources\RedirectResource;
use Filament\Resources\Pages\ListRecords;

class ListRedirect
	extends ListRecords
{
	protected static string $resource   = RedirectResource::class;
	public ?string          $ip_address = null;

	protected function getHeaderActions(): array
	{
		return [];
	}

	protected function getHeaderWidgets(): array
	{
		return RedirectResource::getWidgets();
	}
}
