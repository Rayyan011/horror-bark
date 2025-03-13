<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RideBookingResource\Pages;
use App\Filament\Resources\RideBookingResource\RelationManagers;
use App\Models\RideBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;


class RideBookingResource extends Resource
{
    protected static ?string $model = RideBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),
            Select::make('ferry_slot_id')
                ->relationship('ferrySlot', 'id')
                ->required(),
            TextInput::make('total_price')
                ->numeric(),
            TextInput::make('status')
                ->default('pending'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                
                // Show user name
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                // Show ride name and times
                Tables\Columns\TextColumn::make('rideSlot.ride.name')
                    ->label('Ride')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rideSlot.slot_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rideSlot.start_time')
                    ->label('Start')
                    ->time('H:i'),
                
                // Price and status
                Tables\Columns\TextColumn::make('total_price')
                    ->sortable()
                    ->label('Price'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created')
                    ->sortable(),
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
            'index' => Pages\ListRideBookings::route('/'),
            'create' => Pages\CreateRideBooking::route('/create'),
            'edit' => Pages\EditRideBooking::route('/{record}/edit'),
        ];
    }
}
