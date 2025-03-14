<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RideBookingResource\Pages;
use App\Filament\Resources\RideBookingResource\RelationManagers;
use App\Models\RideBooking;
use App\Models\RideSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;

class RideBookingResource extends Resource
{
    protected static ?string $model = RideBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // 1) User
            Select::make('user_id')
                ->relationship('user', 'name')
                ->label('User')
                ->searchable()
                ->required(),

            // 2) Ride Slot
            Select::make('ride_slot_id')
                ->label('Ride Slot')
                ->options(function () {
                    // Query all ride slots (or add filters if needed)
                    return RideSlot::with('ride')->get()
                        ->mapWithKeys(function ($slot) {
                            // Build a descriptive label
                            $rideName = $slot->ride?->name ?? 'Unknown Ride';
                            $date     = $slot->slot_date->format('Y-m-d');
                            $start    = $slot->start_time->format('H:i');
                            $end      = $slot->end_time->format('H:i');

                            $label = "{$rideName} | {$date} {$start}-{$end}";

                            // Return an array [ slot_id => label ]
                            return [$slot->id => $label];
                        })
                        ->toArray();
                })
                ->searchable()
                ->required(),

                TextInput::make('quantity')
                ->label('Number of People')
                ->numeric()
                ->default(1)
                ->required()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    // When quantity changes, update the total price automatically
                    $rideSlotId = $get('ride_slot_id');
                    if ($rideSlotId) {
                        $slot = \App\Models\RideSlot::with('ride')->find($rideSlotId);
                        if ($slot && $slot->ride && isset($slot->ride->price)) {
                            $price = $slot->ride->price;
                            $set('total_price', $price * $state);
                        }
                    }
                }),

            // 4) Total Price field: calculated automatically (read-only)
            TextInput::make('total_price')
                ->label('Total Price')
                ->numeric()
                ->disabled(),

        ]);
    }

    /**
     * Define columns and table configuration for listing RideBookings.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Primary ID
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // Show user name
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                // Show ride name from the rideSlot->ride relationship
                Tables\Columns\TextColumn::make('rideSlot.ride.name')
                    ->label('Ride')
                    ->searchable()
                    ->sortable(),

                // Show date/time from rideSlot
                Tables\Columns\TextColumn::make('rideSlot.slot_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rideSlot.start_time')
                    ->label('Start')
                    ->time('H:i'),
                // Optionally show end_time if you want
                Tables\Columns\TextColumn::make('rideSlot.end_time')
                    ->label('End')
                    ->time('H:i')
                    ->toggleable(),

                // Price & status
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Price')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),

                // Timestamps
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            // If you want row-level actions, add them here
            ->actions([
                // e.g. Tables\Actions\EditAction::make(),
                // or a custom row action
            ])
            // If you want bulk actions for multiple selected rows
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * No extra relationships in the resource for now.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Define the standard resource pages (list, create, edit, view).
     */
    public static function getPages(): array
    {
        return [
            // Filament pages for listing and CRUD
            'index' => Pages\ListRideBookings::route('/'),
            'create' => Pages\CreateRideBooking::route('/create'),
            'edit' => Pages\EditRideBooking::route('/{record}/edit'),
        ];
    }
}
