<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Filament\Resources\RedirectResource\Widgets\RedirectStatsOverview;
use App\Models\Link;
use App\Models\Redirect;
use Filament\Resources\Resource;
use Filament\Tables;
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
					TextColumn::make('ip_address')
					          ->label('IP адрес'),

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
							      'ip_address',
							      [
								      'ip_address' => $record->ip_address,
							      ]
						      )
					      ),
				]
			)
			->bulkActions(
				[
					Tables\Actions\BulkActionGroup::make(
						[
							Tables\Actions\DeleteBulkAction::make(),
						]
					),
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
			'index'      => Pages\ListRedirectIps::route('/'),
			'ip_address' => Pages\ListRedirectsByIp::route('/{ip_address}'),
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
		               ->selectRaw("MIN($table.id) as id, $table.ip_address, COUNT(*) as redirect_amount")
		               ->groupBy("$table.ip_address");
	}

//	public static function getPluralModelLabel(): string
//	{
//		return 'Переходы с IP ';
//	}
}
