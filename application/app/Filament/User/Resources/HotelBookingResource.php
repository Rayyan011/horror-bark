<?php

namespace App\Filament\User\Resources;

use App\Filament\Resources\Concerns\HasHotelBookingDateRangeFields;
use App\Filament\User\Resources\HotelBookingResource\Pages;
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
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
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
            Hidden::make('user_id')
                ->default(fn () => auth()->id())
                ->required(),

            Select::make('hotel_id')
                ->label('Hotel')
                ->options(fn () => Hotel::where('user_id', auth()->id())->pluck('name', 'id')->toArray())
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => $set('room_id', null))
                ->required(),

            Select::make('room_id')
                ->label('Room')
                ->options(fn ($get) => $get('hotel_id')
                    ? Room::where('hotel_id', $get('hotel_id'))->pluck('room_number', 'id')->toArray()
                    : [])
                ->searchable()
                ->reactive()
                ->afterStateUpdated($recalculatePrice)
                ->required(),

            TextInput::make('quantity')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->reactive()
                ->afterStateUpdated($recalculatePrice)
                ->required(),

            ...self::hotelBookingDateRangeFields($recalculatePrice),

            TextInput::make('total_price')
                ->numeric()
                ->disabled()
                ->required(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'canceled' => 'Canceled',
                ])
                ->default('pending')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('room.room_number')->label('Room')->sortable(),
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
}
