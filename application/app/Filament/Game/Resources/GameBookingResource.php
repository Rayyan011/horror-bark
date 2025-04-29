<?php

namespace App\Filament\Game\Resources;

use App\Filament\Game\Resources\GameBookingResource\Pages;
use App\Filament\Game\Resources\GameBookingResource\RelationManagers;
use App\Models\GameBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameBookingResource extends Resource
{
    protected static ?string $model = GameBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Select the user making the booking
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),
            // Select the game; reactive so that changes update total_price
            Forms\Components\Select::make('game_id')
                ->relationship('game', 'name')
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
            // Booking time field
            Forms\Components\DateTimePicker::make('booking_time')
                ->required(),
            // Quantity field; reactive so that changes update total_price
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
            // Calculated total_price field (read-only but dehydrated)
            Forms\Components\TextInput::make('total_price')
                ->numeric()
                ->disabled()
                ->dehydrated(true)
                ->required(),
            // Booking status field
            Forms\Components\Select::make('status')
                ->options([
                    'pending'   => 'Pending',
                    'confirmed' => 'Confirmed',
                    'cancelled' => 'Cancelled',
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
            'index'  => Pages\ListGameBookings::route('/'),
            'create' => Pages\CreateGameBooking::route('/create'),
            'edit'   => Pages\EditGameBooking::route('/{record}/edit'),
        ];
    }
}