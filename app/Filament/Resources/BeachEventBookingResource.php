<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeachEventBookingResource\Pages;
use App\Models\BeachEventBooking;
use App\Models\BeachEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class BeachEventBookingResource extends Resource
{
    protected static ?string $model = BeachEventBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),

            Forms\Components\Select::make('beach_event_id')
                ->relationship('beachEvent', 'name')
                ->required()
                ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => static::updatePrice($state, $get, $set)),

            Forms\Components\DatePicker::make('booking_date')
                ->required()
                ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => static::validateDate($state, $get, $set)),

            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->minValue(1)
                ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => static::updatePrice($state, $get, $set)),

            Forms\Components\TextInput::make('total_price')
                ->disabled()
                ->required(),

            Forms\Components\Select::make('status')
                ->options([
                    'pending'   => 'Pending',
                    'confirmed' => 'Confirmed',
                    'cancelled' => 'Cancelled',
                ])
                ->required(),
        ]);
    }

    public static function validateDate($date, Forms\Get $get, Forms\Set $set)
    {
        $eventId = $get->get('beach_event_id'); // ✅ Correct way to get the selected event ID
        $event = BeachEvent::find($eventId);

        if ($event && Carbon::parse($date)->toDateString() !== $event->event_date) {
            $set->set('booking_date', null); // ✅ Correct way to set a value
            throw new \Exception("The booking date must match the beach event date.");
        }
    }

    public static function updatePrice($beachEventId, Forms\Get $get, Forms\Set $set)
    {
        $event = BeachEvent::find($beachEventId);
        if ($event) {
           $set->set('total_price', $event->price * $get->get('quantity', 1));

        }
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('User'),
            Tables\Columns\TextColumn::make('beachEvent.name')->label('Beach Event'),
            Tables\Columns\TextColumn::make('booking_date')->date(),
            Tables\Columns\TextColumn::make('quantity'),
            Tables\Columns\TextColumn::make('total_price'),
            Tables\Columns\TextColumn::make('status')->badge()
                ->colors([
                    'pending'   => 'gray',
                    'confirmed' => 'green',
                    'cancelled' => 'red',
                ]),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBeachEventBookings::route('/'),
            'create' => Pages\CreateBeachEventBooking::route('/create'),
            'edit'   => Pages\EditBeachEventBooking::route('/{record}/edit'),
        ];
    }
}
