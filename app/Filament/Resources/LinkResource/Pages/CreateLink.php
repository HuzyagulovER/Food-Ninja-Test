<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLink
	extends CreateRecord
{
	protected static string  $resource = LinkResource::class;
	protected static ?string $title    = 'Новая ссылка';

	protected function mutateFormDataBeforeCreate(array $data): array
	{
		$data['user_id'] = auth()->id();

		return $data;
	}

	protected function getRedirectUrl(): string
	{
		return $this->getResource()::getUrl('index');
	}
}
