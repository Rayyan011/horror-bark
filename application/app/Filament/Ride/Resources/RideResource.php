<?php

namespace App\Filament\Ride\Resources;

use App\Filament\Ride\Resources\RideResource\Pages;
use App\Models\Ride;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;

class RideResource extends Resource
{
    protected static ?string $model = Ride::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('user_id')->default(fn () => auth()->id())->required(),
            TextInput::make('name')->required(),
            TextInput::make('price')->numeric()->required(),
            TextInput::make('latitude')->numeric()->required(),
            TextInput::make('longitude')->numeric()->required(),
            TextInput::make('max_capacity')->numeric()->required(),
            TextInput::make('max_booking_quantity')->numeric()->required(),
            FileUpload::make('images')
                ->label('Ride Images')
                ->directory('rides/gallery')
                ->multiple()
                ->image()
                ->maxFiles(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->sortable(),
            Tables\Columns\TextColumn::make('price')->sortable(),
            Tables\Columns\TextColumn::make('max_capacity')->sortable(),
            Tables\Columns\TextColumn::make('max_booking_quantity')->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRides::route('/'),
            'create' => Pages\CreateRide::route('/create'),
            'edit' => Pages\EditRide::route('/{record}/edit'),
        ];
    }
}
