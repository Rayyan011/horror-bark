<?php

namespace App\Filament\Ferry\Resources;

use App\Filament\Ferry\Resources\FerryBookingResource\Pages;
use App\Filament\Ferry\Resources\FerryBookingResource\RelationManagers;
use App\Models\FerryBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                ->required()
                ->label('Booking Time'),   //add validation between for booking between 9am and 4pm

                TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                TextInput::make('total_price')
                    ->numeric()
                    ->required()
                    ->prefix('MVR'),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
        return [
            //
        ];
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
