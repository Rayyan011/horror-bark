<?php

namespace App\Filament\Hotel\Resources;

use App\Filament\Hotel\Resources\HotelResource\Pages;
use App\Models\Hotel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required(),

            Forms\Components\TextInput::make('location')
                ->required(),

            Forms\Components\TextInput::make('latitude')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('longitude')
                ->numeric()
                ->required(),

            Forms\Components\FileUpload::make('images')
                ->label('Gallery Images')
                ->directory('hotels/gallery')
                ->multiple()
                ->maxFiles(5)
                ->image()
                ->maxSize(1024),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->sortable(),
            Tables\Columns\TextColumn::make('location')->sortable(),
            Tables\Columns\TextColumn::make('latitude')->sortable(),
            Tables\Columns\TextColumn::make('longitude')->sortable(),
            Tables\Columns\ImageColumn::make('images')
                ->disk('public')
                ->getStateUsing(fn ($record) => $record->images[0] ?? null)
                ->size(50)
                ->label('Image'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
