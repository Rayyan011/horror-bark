<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelResource\Pages;
use App\Models\Hotel;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Dotswan\MapPicker\Fields\Map;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Basic hotel details
            Forms\Components\Select::make('user_id')
                ->label('Owner')
                ->options(User::query()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('location')
                ->label('Location Name')
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Section::make('Public Map Placement')
                ->schema([
                    Forms\Components\Placeholder::make('horror_map_picker')
                        ->hiddenLabel()
                        ->content(new \Illuminate\Support\HtmlString(view('filament.forms.components.horror-map-picker')->render())),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('map_x')
                                ->label('Map X')
                                ->numeric()
                                ->default(50)
                                ->readOnly()
                                ->extraInputAttributes(['data-horror-map-x' => '1']),
                            Forms\Components\TextInput::make('map_y')
                                ->label('Map Y')
                                ->numeric()
                                ->default(50)
                                ->readOnly()
                                ->extraInputAttributes(['data-horror-map-y' => '1']),
                        ]),
                ])
                ->columnSpanFull(),
            Map::make('location_data')
                ->label('Legacy Real-World Position')
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
                ->numeric(),
            Forms\Components\TextInput::make('longitude')
                ->label('Longitude')
                ->numeric(),

            Forms\Components\FileUpload::make('images')
                ->label('Additional Images')
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit'   => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
