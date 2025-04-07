<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelBookingResource\Pages;
use App\Filament\Resources\HotelBookingResource\RelationManagers;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;

class HotelBookingResource extends Resource
{
    protected static ?string $model = HotelBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        // Closure to recalculate the price whenever a reactive field updates.
        $recalculatePrice = function (callable $get, callable $set) {
            $roomId = $get('room_id');
            $startDate = $get('start_date');
            $endDate = $get('end_date');
            $quantity = $get('quantity') ?: 1;
            if ($roomId && $startDate && $endDate) {
                $room = Room::find($roomId);
                if ($room) {
                    $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                    $price = $room->price * $days * $quantity;
                    $set('total_price', $price);
                }
            }
        };

        return $form->schema([
            Select::make('user_id')
                ->relationship('user', 'name')
                ->label('User')
                ->searchable()
                ->required(),

            // Hotel selection used to filter available rooms (not persisted).
            Select::make('hotel_id')
                ->label('Hotel')
                ->options(function () {
                    return Hotel::pluck('name', 'id')->toArray();
                })
                ->reactive()
                ->afterStateUpdated(fn($state, callable $set) => $set('room_id', null))
                ->dehydrated(false)
                ->required(),

            // Room selection filtered by the selected hotel.
            Select::make('room_id')
                ->label('Room')
                ->options(function ($get) {
                    $hotelId = $get('hotel_id');
                    if (!$hotelId) {
                        return [];
                    }
                    return Room::where('hotel_id', $hotelId)
                        ->pluck('room_number', 'id')
                        ->toArray();
                })
                ->searchable()
                ->reactive()
                ->afterStateUpdated($recalculatePrice)
                ->required(),

            // Quantity field added for multiple bookings.
            TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->reactive()
                ->afterStateUpdated($recalculatePrice)
                ->required(),

            // Booking start date.
            DatePicker::make('start_date')
                ->label('Start Date')
                ->reactive()
                ->afterStateUpdated($recalculatePrice)
                ->required(),

            // Booking end date.
            DatePicker::make('end_date')
                ->label('End Date')
                ->reactive()
                ->afterStateUpdated($recalculatePrice)
                ->required(),

            // Total price is computed and displayed as read-only.
            TextInput::make('total_price')
                ->label('Total Price')
                ->numeric()
                ->disabled()
                ->reactive()
                ->required(),

            // Booking status.
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
        return $table->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->sortable(),
            Tables\Columns\TextColumn::make('user.name')
                ->label('User')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('room.room_number')
                ->label('Room')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('start_date')
                ->label('Start Date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('end_date')
                ->label('End Date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('total_price')
                ->label('Total Price')
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
            'index'  => Pages\ListHotelBookings::route('/'),
            'create' => Pages\CreateHotelBooking::route('/create'),
            'edit'   => Pages\EditHotelBooking::route('/{record}/edit'),
        ];
    }
}