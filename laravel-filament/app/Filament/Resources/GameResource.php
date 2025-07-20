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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

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
            ])

            ->filters([
                SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Filter by Category'),

                SelectFilter::make('platforms')
                    ->relationship('platforms', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Filter by Platform'),

                SelectFilter::make('release_year')
                    ->options([
                        '2024' => '2024',
                        '2023' => '2023',
                        '2022' => '2022',
                        '2021' => '2021',
                        '2020' => '2020',
                        '2019' => '2019',
                        '2018' => '2018',
                        '2017' => '2017',
                        '2016' => '2016',
                        '2015' => '2015',
                        'older' => 'Before 2015',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, $year): Builder {
                                if ($year === 'older') {
                                    return $query->whereYear('release_date', '<', 2015);
                                }
                                return $query->whereYear('release_date', $year);
                            }
                        );
                    })
                    ->label('Release Year'),

                Filter::make('recent_releases')
                    ->query(fn (Builder $query): Builder => $query->where('release_date', '>=', Carbon::now()->subDays(30)))
                    ->label('Recent Releases (Last 30 days)'),

                Filter::make('upcoming_releases')
                    ->query(fn (Builder $query): Builder => $query->where('release_date', '>', Carbon::now()))
                    ->label('Upcoming Releases'),

                Filter::make('has_cover_image')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('cover_image'))
                    ->label('Has Cover Image'),

                Filter::make('no_cover_image')
                    ->query(fn (Builder $query): Builder => $query->whereNull('cover_image'))
                    ->label('Missing Cover Image'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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