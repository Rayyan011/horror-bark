<?php

namespace App\Filament\Game\Resources;

use App\Filament\Game\Resources\GameResource\Pages;
use App\Filament\Game\Resources\GameResource\RelationManagers;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Owner of the game
            Forms\Components\Select::make('user_id')
                ->label('Owner')
                ->relationship('owner', 'name')
                ->required(),
            Forms\Components\TextInput::make('name')
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
            Map::make('location_data')
                ->label('Select Location on Map')
                ->columnSpanFull()
                ->defaultLocation(latitude: 4.22700104517645, longitude: 73.42662978621766)
                ->draggable(true)
                ->clickable(true)
                ->zoom(16)
                ->minZoom(0)
                ->maxZoom(28)
                ->tilesUrl("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}")
                ->detectRetina(true)
                ->showMarker(true)
                ->markerColor("#3b82f6")
                ->showFullscreenControl(true)
                ->afterStateHydrated(function ($state, $record, Set $set): void {
                    if ($record && $record->latitude && $record->longitude) {
                        $set('location_data', [
                            'lat' => $record->latitude,
                            'lng' => $record->longitude,
                        ]);
                    }
                })
                ->afterStateUpdated(function ($state, Set $set): void {
                    if (is_array($state)) {
                        $set('latitude', $state['lat'] ?? null);
                        $set('longitude', $state['lng'] ?? null);
                    }
                })
                ->showZoomControl(true),

            Forms\Components\TextInput::make('latitude')
                ->label('Latitude')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('longitude')
                ->label('Longitude')
                ->numeric()
                ->required(),

            Forms\Components\FileUpload::make('images')
                ->label('Additional Images')
                ->directory('games/gallery')
                ->multiple()
                ->maxFiles(5)
                ->image()
                ->maxSize(1024),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('owner.name')
                ->label('Owner')
                ->sortable(),
            Tables\Columns\TextColumn::make('name')
                ->sortable(),
            Tables\Columns\TextColumn::make('price')
                ->sortable(),
            Tables\Columns\TextColumn::make('max_capacity')
                ->sortable(),
            Tables\Columns\TextColumn::make('max_booking_quantity')
                ->sortable(),
            Tables\Columns\TextColumn::make('latitude')
                ->label('Latitude')
                ->sortable(),
            Tables\Columns\TextColumn::make('longitude')
                ->label('Longitude')
                ->sortable(),
            Tables\Columns\ImageColumn::make('images')
                ->disk('public')
                ->getStateUsing(fn ($record) => $record->images[0] ?? null)
                ->size(50)
                ->label('Gallery'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit'   => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}