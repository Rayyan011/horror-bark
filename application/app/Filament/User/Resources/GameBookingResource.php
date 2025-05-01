<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\GameBookingResource\Pages;
use App\Filament\User\Resources\GameBookingResource\RelationManagers;
use App\Models\GameBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameBookingResource extends Resource
{
    protected static ?string $model = GameBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            'index' => Pages\ListGameBookings::route('/'),
            'create' => Pages\CreateGameBooking::route('/create'),
            'edit' => Pages\EditGameBooking::route('/{record}/edit'),
        ];
    }
}
