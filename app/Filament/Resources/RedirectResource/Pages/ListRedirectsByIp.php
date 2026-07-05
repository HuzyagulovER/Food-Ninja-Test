<?php

namespace App\Filament\Resources\RedirectResource\Pages;

use App\Filament\Resources\RedirectResource;
use App\Filament\Resources\RedirectResource\Widgets\RedirectByIpsStatsOverview;
use App\Models\Redirect;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListRedirectsByIp
	extends Page
	implements HasTable
{
	use InteractsWithTable;

	protected static string $resource = RedirectResource::class;
	protected static string $view     = 'filament.resources.redirect-resource.pages.redirects-by-ip';

	public string $ipAddress;

	public function mount(string $ip_address): void
	{
		$this->ipAddress = $ip_address;
	}

	public function table(Table $table): Table
	{
		return $table
			->query(
				Redirect::query()
				        ->where('ip_address', $this->ipAddress)
			)
			->defaultSort('created_at', 'desc')
			->columns(
				[
					TextColumn::make('ip_address')
					          ->label('IP'),

					TextColumn::make('created_at')
					          ->label('Дата')
					          ->dateTime('Y-m-d H:i:s')
					          ->sortable(),
				]
			);
	}

	public function getTitle(): Htmlable|string
	{
		return 'Переходы с IP ' . $this->ipAddress;
	}

	protected function getHeaderWidgets(): array
	{
		return [
			RedirectByIpsStatsOverview::make(
				[
					'ip_address' => $this->ipAddress,
				]
			),
		];
	}
}
