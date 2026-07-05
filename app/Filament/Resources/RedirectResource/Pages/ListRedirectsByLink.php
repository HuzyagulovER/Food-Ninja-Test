<?php

namespace App\Filament\Resources\RedirectResource\Pages;

use App\Filament\Resources\RedirectResource;
use App\Filament\Resources\RedirectResource\Widgets\RedirectByIpsStatsOverview;
use App\Models\Redirect;
use App\Services\Symlink;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListRedirectsByLink
	extends Page
	implements HasTable
{
	use InteractsWithTable;

	protected static string $resource = RedirectResource::class;
	protected static string $view     = 'filament.resources.redirect-resource.pages.redirects-by-link';

	public string $linkId;
	public string $encodedLink;

	public function mount(string $link_id): void
	{
		$this->linkId      = $link_id;
		$this->encodedLink = Symlink::encode($this->linkId);
	}

	public function table(Table $table): Table
	{
		return $table
			->query(
				Redirect::query()
				        ->where('link_id', $this->linkId)
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
		return 'Переходы по ссылке ' . $this->encodedLink;
	}

	protected function getHeaderWidgets(): array
	{
		return [
			RedirectByIpsStatsOverview::make(
				[
					'link_id' => $this->linkId,
				]
			),
		];
	}
}
