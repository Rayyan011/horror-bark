<?php

namespace App\Filament\Game\Resources;

use App\Filament\Game\Resources\GameResource\Pages;
use App\Models\Game;
use App\Services\IslandAccessService;
use App\Support\AdminImage;
use App\Support\HorrorDistrictCatalog;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
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
                ->preload()
                ->native(false)
                ->helperText('Games are available on Horror Island only.'),

            Forms\Components\Select::make('location')
                ->label('District')
                ->options(HorrorDistrictCatalog::horrorLocations())
                ->searchable()
                ->preload()
                ->native(false),

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
            Tables\Columns\TextColumn::make('location')
                ->label('District')
                ->sortable(),
            Tables\Columns\ImageColumn::make('images')
                ->getStateUsing(fn ($record) => AdminImage::first($record->images))
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
