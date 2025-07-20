<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Admins';

    protected static ?string $modelLabel = 'Admin';

    protected static ?string $pluralModelLabel = 'Admins';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->helperText('Leave blank to keep current password when editing'),

                Forms\Components\Toggle::make('email_verified_at')
                    ->label('Email Verified')
                    ->dehydrated()
                    ->dehydrateStateUsing(fn ($state) => $state ? now() : null)
                    ->formatStateUsing(fn ($state) => $state !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter by Email Verification Status
                SelectFilter::make('email_verified')
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, $status): Builder {
                                if ($status === 'verified') {
                                    return $query->whereNotNull('email_verified_at');
                                }
                                return $query->whereNull('email_verified_at');
                            }
                        );
                    })
                    ->label('Email Status'),

                // Filter by Registration Date
                SelectFilter::make('registration_period')
                    ->options([
                        'today' => 'Today',
                        'this_week' => 'This Week',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'this_year' => 'This Year',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, $period): Builder {
                                return match($period) {
                                    'today' => $query->whereDate('created_at', Carbon::today()),
                                    'this_week' => $query->whereBetween('created_at', [
                                        Carbon::now()->startOfWeek(),
                                        Carbon::now()->endOfWeek()
                                    ]),
                                    'this_month' => $query->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year),
                                    'last_month' => $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                                        ->whereYear('created_at', Carbon::now()->subMonth()->year),
                                    'this_year' => $query->whereYear('created_at', Carbon::now()->year),
                                };
                            }
                        );
                    })
                    ->label('Registration Period'),

                // Filter by Recent Activity (Updated Recently)
                Filter::make('recently_updated')
                    ->query(fn (Builder $query): Builder => $query->where('updated_at', '>=', Carbon::now()->subDays(7)))
                    ->label('Updated in Last 7 Days'),

                // Filter by New Admins (Last 30 days)
                Filter::make('new_admins')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', Carbon::now()->subDays(30)))
                    ->label('New Admins (Last 30 Days)'),

                // Filter by Admins with Remember Token (Currently logged in somewhere)
                Filter::make('has_remember_token')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('remember_token'))
                    ->label('Has Active Sessions'),

                // Filter by Admin Name Length (for data quality)
                SelectFilter::make('name_completeness')
                    ->options([
                        'complete' => 'Complete Names (5+ characters)',
                        'short' => 'Short Names (< 5 characters)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, $type): Builder {
                                if ($type === 'complete') {
                                    return $query->whereRaw('LENGTH(name) >= 5');
                                }
                                return $query->whereRaw('LENGTH(name) < 5');
                            }
                        );
                    })
                    ->label('Name Completeness'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}