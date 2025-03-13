<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RideResource\Pages;
use App\Filament\Resources\RideResource\RelationManagers;
use App\Models\Ride;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;


class RideResource extends Resource
{
    protected static ?string $model = Ride::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('Owner (User)')
                ->relationship('user', 'name')
                ->required(),

            TextInput::make('name')
                ->label('Ride Name')
                ->required(),
            Textarea::make('description')
                ->label('Description')
                ->rows(3),
            TextInput::make('default_capacity')
                ->numeric()
                ->default(0)
                ->required(),
            TimePicker::make('open_time'),
            TimePicker::make('close_time'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('default_capacity'),
                Tables\Columns\TextColumn::make('open_time')->time('H:i'),
                Tables\Columns\TextColumn::make('close_time')->time('H:i'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
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
            'index' => Pages\ListRides::route('/'),
            'create' => Pages\CreateRide::route('/create'),
            'edit' => Pages\EditRide::route('/{record}/edit'),
        ];
    }
}
