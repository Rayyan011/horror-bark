<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\GameBookingResource\Pages;
use App\Models\Game;
use App\Models\GameBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

class GameBookingResource extends Resource
{
    protected static ?string $model = GameBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        $calculateTotal = function (callable $get, callable $set) {
            $gameId = $get('game_id');
            $quantity = $get('quantity') ?? 0;
            if ($gameId) {
                $game = Game::find($gameId);
                if ($game) {
                    $set('total_price', $game->price * $quantity);
                }
            }
        };

        return $form->schema([
            Hidden::make('user_id')
                ->default(fn () => auth()->id())
                ->required(),

            Select::make('game_id')
                ->relationship('game', 'name')
                ->reactive()
                ->afterStateUpdated($calculateTotal)
                ->required(),

            DateTimePicker::make('booking_time')
                ->required(),

            TextInput::make('quantity')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->reactive()
                ->afterStateUpdated($calculateTotal)
                ->required(),

            TextInput::make('total_price')
                ->numeric()
                ->disabled()
                ->dehydrated(true)
                ->required(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'cancelled' => 'Cancelled',
                ])
                ->default('pending')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('game.name')->sortable(),
            Tables\Columns\TextColumn::make('booking_time')->sortable()->dateTime(),
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
