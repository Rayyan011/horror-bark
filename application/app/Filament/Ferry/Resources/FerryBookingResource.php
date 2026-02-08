<?php

namespace App\Filament\Ferry\Resources;

use App\Filament\Ferry\Resources\FerryBookingResource\Pages;
use App\Models\FerryBooking;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FerryBookingResource extends Resource
{
    protected static ?string $model = FerryBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Select::make('ferry_id')
                    ->relationship('ferry', 'name')
                    ->required(),

                DateTimePicker::make('booking_time')
                    ->label('Booking Time')
                    ->required(),

                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                TextInput::make('total_price')
                    ->numeric()
                    ->prefix('MVR')
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('ferry.name')->label('Ferry'),
                Tables\Columns\TextColumn::make('booking_time')->label('Booking Time')->dateTime('Y-m-d H:i'),
                Tables\Columns\TextColumn::make('quantity')->label('Qty'),
                Tables\Columns\TextColumn::make('total_price')->money('MVR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'canceled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFerryBookings::route('/'),
            'create' => Pages\CreateFerryBooking::route('/create'),
            'edit' => Pages\EditFerryBooking::route('/{record}/edit'),
        ];
    }
}
