<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\Category;
use App\Models\Platform;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->rows(10)
                    ->cols(20)
                    ->required(),
                Forms\Components\FileUpload::make('cover_image')
                    ->disk('public')
                    ->directory('images')
                    ->required()
                    ->image(),
                Forms\Components\DatePicker::make('release_date')
                    ->required()
                    ->maxDate(now()),
                Forms\Components\Select::make('categories')
                    ->label('Categories')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->preload(),

                Forms\Components\Select::make('platforms')
                    ->label('Platforms')
                    ->multiple()
                    ->relationship('platforms', 'name')
                    ->preload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('name')
                   ->sortable()
                   ->searchable(),

               Tables\Columns\TextColumn::make('description')
                   ->limit(100)
                   ->wrap()
                   ->tooltip(fn ($record) => $record->description),

               Tables\Columns\ImageColumn::make('cover_image')
                   ->square()
                   ->disk('public')
                   ->height(50),

               Tables\Columns\TextColumn::make('release_date')
                   ->date()
                   ->sortable(),

               Tables\Columns\TextColumn::make('categories.name')
                   ->label('Categories'),

               Tables\Columns\TextColumn::make('platforms.name')
                   ->label('Platforms'),
            ]);
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
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
