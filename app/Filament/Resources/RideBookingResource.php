<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RideBookingResource\Pages;
use App\Models\RideBooking;
use App\Models\HotelBooking;
use App\Models\RideSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class RideBookingResource extends Resource
{
    protected static ?string $model = RideBooking::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        // Function to recalculate the total price based on ride slot price and quantity.
        $recalculatePrice = function (callable $get, callable $set): void {
            $rideSlotId = $get('ride_slot_id');
            $quantity   = $get('quantity') ?: 1;

            if ($rideSlotId) {
                $slot = RideSlot::with('ride')->find($rideSlotId);
                if ($slot && $slot->ride && isset($slot->ride->price)) {
                    $price = $slot->ride->price;
                    $set('total_price', $price * $quantity);
                    return;
                }
            }
            $set('total_price', 0);
        };

        return $form->schema([
            // User selection field
            Select::make('user_id')
                ->relationship('user', 'name')
                ->label('User')
                ->searchable()
                ->required(),

            // Ride Slot selection field
            Select::make('ride_slot_id')
                ->label('Ride Slot')
                ->options(function () {
                    return RideSlot::with('ride')->get()
                        ->mapWithKeys(function ($slot) {
                            $rideName = $slot->ride?->name ?? 'Unknown Ride';
                            $date     = $slot->slot_date->format('Y-m-d');
                            $start    = $slot->start_time->format('H:i');
                            $end      = $slot->end_time->format('H:i');
                            $label    = "{$rideName} | {$date} {$start}-{$end}";
                            return [$slot->id => $label];
                        })
                        ->toArray();
                })
                ->live() // live updating instead of reactive
                ->afterStateUpdated(fn ($state, callable $set, callable $get) => $recalculatePrice($get, $set))
                ->required(),

            // Quantity field
            TextInput::make('quantity')
                ->label('Number of People')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->live() // use live updates
                ->afterStateUpdated(fn ($state, callable $set, callable $get) => $recalculatePrice($get, $set))
                ->required(),

            // Total Price field (read-only; it displays the calculated value)
            TextInput::make('total_price')
                ->label('Total Price')
                ->numeric()
                ->readOnly(),

            // Status field
            Select::make('status')
                ->label('Status')
                ->options([
                    'pending'   => 'Pending',
                    'confirmed' => 'Confirmed',
                    'cancelled' => 'Cancelled',
                ])
                ->default('pending')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('rideSlot.end_time')
                    ->label('End')
                    ->time('H:i')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Price')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index'  => Pages\ListRideBookings::route('/'),
            'create' => Pages\CreateRideBooking::route('/create'),
            'edit'   => Pages\EditRideBooking::route('/{record}/edit'),
        ];
    }
}
