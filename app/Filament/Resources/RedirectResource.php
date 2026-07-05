<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Filament\Resources\RedirectResource\Widgets\RedirectStatsOverview;
use App\Models\Link;
use App\Models\Redirect;
use App\Services\Symlink;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RedirectResource
	extends Resource
{
	protected static ?string $model            = Redirect::class;
	protected static ?string $navigationLabel  = 'Редиректы';
	protected static ?string $pluralModelLabel = 'Редиректы';
	protected static ?string $navigationIcon   = 'heroicon-o-share';

	public static function table(Table $table): Table
	{
		return $table
			->columns(
				[
					TextColumn::make('symlink')
					          ->label('Ссылка')
					          ->getStateUsing(fn ($record): string => Symlink::encode($record->link)),

					TextColumn::make('original_link')
					          ->label('Оригинальная ссылка')
					          ->tooltip(fn (string $state): string => $state)
					          ->limit(40, '...'),

					TextColumn::make('redirect_amount')
					          ->label('Количество переходов'),
				]
			)
			->filters(
				[
					//
				]
			)
			->actions(
				[
					Action::make('view')
					      ->label('Просмотр статистики')
					      ->icon('heroicon-o-eye')
					      ->url(
						      fn ($record) => self::getUrl(
							      'link',
							      [
								      'link_id' => $record->link_id,
							      ]
						      )
					      ),
				]
			)
			->bulkActions(
				[
					//
				]
			);
	}

	public static function getRelations(): array
	{
		return [
			//
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListRedirect::route('/'),
			'link'  => Pages\ListRedirectsByLink::route('/{link_id}'),
		];
	}

	public static function getWidgets(): array
	{
		return [
			RedirectStatsOverview::class,
		];
	}

	public static function getEloquentQuery(): Builder
	{
		$linkTable = (new Link())->getTable();
		$table     = (new Redirect())->getTable();

		return Redirect::query()
		               ->join($linkTable, "$table.link_id", '=', "$linkTable.id")
		               ->where("$linkTable.user_id", auth()->id())
		               ->selectRaw(
			               "MIN($table.id) as id, link as original_link, link_id, COUNT(*) as redirect_amount"
		               )
		               ->groupBy(["link", "link_id"]);
	}
}
