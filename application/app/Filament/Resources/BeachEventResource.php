<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeachEventResource\Pages;
use App\Filament\Resources\BeachEventResource\RelationManagers;
use App\Models\BeachEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

class BeachEventResource extends Resource
{
    protected static ?string $model = BeachEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            // Organizer (owner) selection
            Forms\Components\Select::make('user_id')
                ->relationship('owner', 'name')
                ->required(),
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\DatePicker::make('event_date')
                ->required(),
            Forms\Components\TextInput::make('price')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('max_capacity')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('max_booking_quantity')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('owner.name')->label('Organizer'),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('event_date')->date(),
            Tables\Columns\TextColumn::make('price'),
            Tables\Columns\TextColumn::make('max_capacity'),
            Tables\Columns\TextColumn::make('max_booking_quantity'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
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
            'index'  => Pages\ListBeachEvents::route('/'),
            'create' => Pages\CreateBeachEvent::route('/create'),
            'edit'   => Pages\EditBeachEvent::route('/{record}/edit'),
        ];
    }
}