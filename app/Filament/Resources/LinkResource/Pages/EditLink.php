<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLink
	extends EditRecord
{
	protected static string  $resource = LinkResource::class;
	protected static ?string $title    = 'Редактирование ссылки';

	protected function getHeaderActions(): array
	{
		return [
			Actions\DeleteAction::make(),
		];
	}

	protected function getRedirectUrl(): string
	{
		return $this->getResource()::getUrl('index');
	}
}
