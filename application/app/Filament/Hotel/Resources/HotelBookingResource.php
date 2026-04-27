<?php

namespace App\Filament\Hotel\Resources;

use App\Filament\Hotel\Resources\HotelBookingResource\Pages;
use App\Filament\Resources\Concerns\HasHotelBookingDateRangeFields;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Room;
use Carbon\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HotelBookingResource extends Resource
{
    use HasHotelBookingDateRangeFields;

    protected static ?string $model = HotelBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('room.hotel', fn (Builder $query) => $query->where('user_id', auth()->id()));
    }

    public static function form(Form $form): Form
    {
        $recalculatePrice = function (callable $get, callable $set) {
            $roomId = $get('room_id');
            $startDate = $get('start_date');
            $endDate = $get('end_date');
            $quantity = $get('quantity') ?: 1;
            if ($roomId && $startDate && $endDate) {
                $room = Room::query()
                    ->whereKey($roomId)
                    ->whereHas('hotel', fn (Builder $query) => $query->where('user_id', auth()->id()))
                    ->first();

                if ($room) {
                    $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                    $price = $room->price * $days * $quantity;
                    $set('total_price', $price);
                }
            }
        };

        return $form->schema([
            Hidden::make('user_id')
                ->default(fn () => auth()->id())
                ->required(),

            Select::make('hotel_id')
                ->label('Hotel')
                ->options(fn () => RoomResource::ownedHotelOptions())
                ->searchable()
                ->preload()
                ->native(false)
                ->rules([self::ownedHotelValidationRule()])
                ->reactive()
                ->afterStateHydrated(function (Select $component, ?HotelBooking $record): void {
                    $component->state($record?->room?->hotel_id);
                })
                ->afterStateUpdated(fn ($state, callable $set) => $set('room_id', null))
                ->dehydrated(false)
                ->required(),

            Select::make('room_id')
                ->label('Room')
                ->options(fn ($get) => RoomResource::ownedRoomOptions((int) $get('hotel_id')))
                ->searchable()
                ->preload()
                ->native(false)
                ->rules([self::ownedRoomValidationRule()])
                ->reactive()
                ->afterStateUpdated($recalculatePrice)
                ->required(),

            TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->reactive()
                ->afterStateUpdated($recalculatePrice)
                ->required(),

            ...self::hotelBookingDateRangeFields($recalculatePrice),

            TextInput::make('total_price')
                ->label('Total Price')
                ->numeric()
                ->disabled()
                ->reactive()
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'canceled' => 'Canceled',
                ])
                ->native(false)
                ->default('pending')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('room.room_number')->label('Room')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('total_price')->sortable(),
            Tables\Columns\TextColumn::make('status')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created')->sortable(),
        ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotelBookings::route('/'),
            'create' => Pages\CreateHotelBooking::route('/create'),
            'edit' => Pages\EditHotelBooking::route('/{record}/edit'),
        ];
    }

    private static function ownedHotelValidationRule(): \Closure
    {
        return function () {
            return function (string $attribute, $value, $fail): void {
                $ownsHotel = Hotel::query()
                    ->whereKey($value)
                    ->where('user_id', auth()->id())
                    ->exists();

                if (! $ownsHotel) {
                    $fail('Select one of your hotels.');
                }
            };
        };
    }

    private static function ownedRoomValidationRule(): \Closure
    {
        return function () {
            return function (string $attribute, $value, $fail): void {
                $ownsRoom = Room::query()
                    ->whereKey($value)
                    ->whereHas('hotel', fn (Builder $query) => $query->where('user_id', auth()->id()))
                    ->exists();

                if (! $ownsRoom) {
                    $fail('Select one of your rooms.');
                }
            };
        };
    }
}
