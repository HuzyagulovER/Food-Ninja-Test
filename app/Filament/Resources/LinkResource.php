<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages\CreateLink;
use App\Filament\Resources\LinkResource\Pages\EditLink;
use App\Filament\Resources\LinkResource\Pages\ListLinks;
use App\Models\Link;
use App\Services\Symlink;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LinkResource
	extends Resource
{
	protected static ?string $model            = Link::class;
	protected static ?string $pluralModelLabel = 'Ссылки';
	protected static ?string $navigationLabel  = 'Ссылки';
	protected static ?string $navigationIcon   = 'heroicon-o-link';

	public static function form(Form $form): Form
	{
		return $form
			->schema(
				[
					TextInput::make('title')
					         ->name('Название')
					         ->required()
					         ->maxLength(64),
					Textarea::make('link')
					        ->name('Ссылка')
					        ->required()
					        ->columnSpanFull(),
				]
			);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns(
				[
					ImageColumn::make('favicon')
					           ->label('')
					           ->getStateUsing(
					           /**
					            * Можно было бы добавить кеширование, но пока не стал
					            */
						           function ($record): string {
							           $host = parse_url($record->link, PHP_URL_HOST);
							           return "https://www.google.com/s2/favicons?domain={$host}";
						           }
					           )
					           ->width(24)
					           ->height(24),
					TextColumn::make('title')
					          ->label('Название'),
					TextColumn::make('symlink')
					          ->label(view('filament.tables.columns.link-header'))
					          ->getStateUsing(fn ($record): string => Symlink::encode($record))
					          ->copyable(),
					TextColumn::make('link')
					          ->label('Оригинальная ссылка')
					          ->limit(40, '...')
					          ->tooltip(fn (string $state): string => $state),
					TextColumn::make('created_at')
					          ->label('Дата создания')
					          ->dateTime('Y-m-d H:i:s')
					          ->sortable()
					          ->toggleable(isToggledHiddenByDefault: true),
					TextColumn::make('updated_at')
					          ->label('Дата обновления')
					          ->dateTime('Y-m-d H:i:s')
					          ->sortable()
					          ->toggleable(isToggledHiddenByDefault: true),
				]
			)
			->filters(
				[
					//
				]
			)
			->actions(
				[
					EditAction::make(),
				]
			)
			->bulkActions(
				[
					BulkActionGroup::make(
						[
							DeleteBulkAction::make(),
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
			'index'  => ListLinks::route('/'),
			'create' => CreateLink::route('/create'),
			'edit'   => EditLink::route('/{record}/edit'),
		];
	}
}
