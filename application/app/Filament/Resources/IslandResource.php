<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IslandResource\Pages;
use App\Filament\Resources\IslandResource\RelationManagers;
use App\Models\Island;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Dotswan\MapPicker\Fields\Map;


class IslandResource extends Resource
{
    protected static ?string $model = Island::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                Select::make('type')
                    ->options([
                        'Horror-Island' => 'Horror Island',
                        'Picnic-Island' => 'Picnic Island'
                    ]),
                MarkdownEditor::make('description'),
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
                    ->directory('islands/gallery')
                    ->multiple()
                    ->maxFiles(5)
                    ->image()
                    ->maxSize(1024),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->label('Type')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Name')->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Description')->sortable(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListIslands::route('/'),
            'create' => Pages\CreateIsland::route('/create'),
            'edit' => Pages\EditIsland::route('/{record}/edit'),
        ];
    }
}
