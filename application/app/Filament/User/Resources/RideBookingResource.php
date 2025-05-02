<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\RideBookingResource\Pages;
use App\Models\Ride;
use App\Models\RideBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

class RideBookingResource extends Resource
{
    protected static ?string $model = RideBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        $recalculate = function (callable $get, callable $set) {
            $rideId = $get('ride_id');
            $quantity = $get('quantity') ?: 1;
            if ($rideId) {
                $ride = Ride::find($rideId);
                if ($ride) {
                    $set('total_price', $ride->price * $quantity);
                }
            }
        };

        return $form->schema([
            Hidden::make('user_id')
                ->default(fn () => auth()->id())
                ->required(),

            Select::make('ride_id')
                ->label('Ride')
                ->options(fn () => Ride::where('user_id', auth()->id())->pluck('name', 'id')->toArray())
                ->required()
                ->reactive()
                ->afterStateUpdated($recalculate),

            DateTimePicker::make('booking_time')
                ->label('Booking Time')
                ->required()
                ->native(false)
                ->displayFormat('Y-m-d H:i')
                ->rules([
                    function () {
                        return function (string $attribute, $value, $fail) {
                            $time = date('H:i', strtotime($value));
                            if ($time !== '09:00' && $time !== '17:00') {
                                $fail('Booking time must be exactly 09:00 or 17:00.');
                            }
                        };
                    }
                ]),

            TextInput::make('quantity')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->required()
                ->reactive()
                ->afterStateUpdated($recalculate),

            TextInput::make('total_price')
                ->numeric()
                ->disabled()
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
            Tables\Columns\TextColumn::make('ride.name')->label('Ride')->sortable(),
            Tables\Columns\TextColumn::make('booking_time')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('quantity')->sortable(),
            Tables\Columns\TextColumn::make('total_price')->sortable(),
            Tables\Columns\TextColumn::make('status')->sortable(),
        ]);
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
