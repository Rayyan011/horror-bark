<?php

namespace App\Filament\Game\Resources;

use App\Filament\Game\Resources\GameResource\Pages;
use App\Models\Game;
use App\Services\IslandAccessService;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('user_id')
                ->default(fn () => auth()->id())
                ->required(),
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\Textarea::make('description')
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('price')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('max_capacity')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('max_booking_quantity')
                ->numeric()
                ->required(),
            Forms\Components\Select::make('island_id')
                ->label('Island')
                ->relationship('island', 'name', fn ($query) => $query->where('type', IslandAccessService::HORROR_ISLAND))
                ->required()
                ->searchable()
                ->helperText('Games are available on Horror Island only.'),
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
                ->tilesUrl('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}')
                ->detectRetina(true)
                ->showMarker(true)
                ->markerColor('#3b82f6')
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
            Tables\Columns\TextColumn::make('name')
                ->sortable(),
            Tables\Columns\TextColumn::make('price')
                ->sortable(),
            Tables\Columns\TextColumn::make('max_capacity')
                ->sortable(),
            Tables\Columns\TextColumn::make('max_booking_quantity')
                ->sortable(),
            Tables\Columns\TextColumn::make('island.name')
                ->label('Island')
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
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
