<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RideBookingResource\Pages;
use App\Models\RideBooking;
use App\Models\HotelBooking;
use App\Models\Ride;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;

class RideBookingResource extends Resource
{
    protected static ?string $model = RideBooking::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Select user who is booking
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),
            // Select the ride; when changed, recalculate total_price
            Forms\Components\Select::make('ride_id')
                ->relationship('ride', 'name')
                ->reactive()
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $rideId = $get('ride_id');
                    $quantity = $get('quantity') ?? 0;
                    if ($rideId) {
                        $ride = Ride::find($rideId);
                        if ($ride) {
                            $set('total_price', $ride->price * $quantity);
                        }
                    }
                })
                ->required(),
            // Pick booking time with allowed hours
            Forms\Components\DateTimePicker::make('booking_time')
                ->required(),
                
            // Quantity field; when updated, recalculate total_price
            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->minValue(1)
                ->reactive()
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $rideId = $get('ride_id');
                    $quantity = $get('quantity') ?? 0;
                    if ($rideId) {
                        $ride = Ride::find($rideId);
                        if ($ride) {
                            $set('total_price', $ride->price * $quantity);
                        }
                    }
                })
                ->required(),
            // total_price is calculated automatically (read-only)
            Forms\Components\TextInput::make('total_price')
                ->numeric()
                ->disabled()
                ->dehydrated(true)
                ->required(),
            // Status of the booking
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
            Tables\Columns\TextColumn::make('ride.name')->sortable(),
            Tables\Columns\TextColumn::make('booking_time')->sortable(),
            Tables\Columns\TextColumn::make('quantity')->sortable(),
            Tables\Columns\TextColumn::make('total_price')->sortable(),
            Tables\Columns\TextColumn::make('status')->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRideBookings::route('/'),
            'create' => Pages\CreateRideBooking::route('/create'),
            'edit'   => Pages\EditRideBooking::route('/{record}/edit'),
        ];
    }
}