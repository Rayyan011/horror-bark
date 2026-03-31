<?php

namespace App\Filament\Game\Resources;

use App\Filament\Game\Resources\GameBookingResource\Pages;
use App\Models\Game;
use App\Models\GameBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GameBookingResource extends Resource
{
    protected static ?string $model = GameBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('game', fn (Builder $query) => $query->where('user_id', auth()->id()));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('game_id')
                ->label('Game')
                ->options(fn () => Game::query()
                    ->where('user_id', auth()->id())
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray())
                ->searchable()
                ->reactive()
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $gameId = $get('game_id');
                    $quantity = $get('quantity') ?? 0;
                    if ($gameId) {
                        $game = Game::find($gameId);
                        if ($game) {
                            $set('total_price', $game->price * $quantity);
                        }
                    }
                })
                ->required(),
            Forms\Components\DateTimePicker::make('booking_time')
                ->required(),
            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->minValue(1)
                ->reactive()
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $gameId = $get('game_id');
                    $quantity = $get('quantity') ?? 0;
                    if ($gameId) {
                        $game = Game::find($gameId);
                        if ($game) {
                            $set('total_price', $game->price * $quantity);
                        }
                    }
                })
                ->required(),
            Forms\Components\TextInput::make('total_price')
                ->numeric()
                ->disabled()
                ->dehydrated(true)
                ->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'canceled' => 'Canceled',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->sortable(),
            Tables\Columns\TextColumn::make('game.name')->sortable(),
            Tables\Columns\TextColumn::make('booking_time')->sortable(),
            Tables\Columns\TextColumn::make('quantity')->sortable(),
            Tables\Columns\TextColumn::make('total_price')->sortable(),
            Tables\Columns\TextColumn::make('status')->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGameBookings::route('/'),
            'create' => Pages\CreateGameBooking::route('/create'),
            'edit' => Pages\EditGameBooking::route('/{record}/edit'),
        ];
    }
}
