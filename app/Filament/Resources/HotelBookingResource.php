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

class HotelBookingResource extends Resource
{
    protected static ?string $model = HotelBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Select::make('user_id')
                ->relationship('user', 'name')
                ->label('User')
                ->searchable()
                ->required(),
            // Hotel selection field – used to filter available rooms. This field is not stored.
            Select::make('hotel_id')
                ->label('Hotel')
                ->options(function () {
                    return Hotel::pluck('name', 'id')->toArray();
                })
                ->reactive()
                ->afterStateUpdated(fn($state, callable $set) => $set('room_id', null))
                ->dehydrated(false) // Do not persist this field; it's only used for filtering.
                ->required(),

            // Room selection, filtered by the selected hotel.
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
                ->required(),

            // Booking start date
            DatePicker::make('start_date')
                ->label('Start Date')
                ->required(),

            // Booking end date
            DatePicker::make('end_date')
                ->label('End Date')
                ->required(),

            // Total price (entered manually or can be dynamically calculated later)
            TextInput::make('total_price')
                ->label('Total Price')
                ->numeric()
                ->required(),

            // Booking status
            TextInput::make('status')
                ->label('Status')
                ->default('pending')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // Primary booking ID
            Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->sortable(),

            // Display user name
            Tables\Columns\TextColumn::make('user.name')
                ->label('User')
                ->searchable()
                ->sortable(),

            // Display room number
            Tables\Columns\TextColumn::make('room.room_number')
                ->label('Room')
                ->searchable()
                ->sortable(),

            // Display start date
            Tables\Columns\TextColumn::make('start_date')
                ->label('Start Date')
                ->date()
                ->sortable(),

            // Display end date
            Tables\Columns\TextColumn::make('end_date')
                ->label('End Date')
                ->date()
                ->sortable(),

            // Total price column
            Tables\Columns\TextColumn::make('total_price')
                ->label('Total Price')
                ->sortable(),

            // Booking status
            Tables\Columns\TextColumn::make('status')
                ->sortable(),

            // Creation timestamp
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
            'index' => Pages\ListHotelBookings::route('/'),
            'create' => Pages\CreateHotelBooking::route('/create'),
            'edit' => Pages\EditHotelBooking::route('/{record}/edit'),
        ];
    }
}
