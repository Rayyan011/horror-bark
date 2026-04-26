<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Game;
use App\Models\Island;
use App\Services\IslandAccessService;
use Filament\Forms\Form;
use Filament\Forms;
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
                ->searchable(['name', 'email'])
                ->preload()
                ->native(false)
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
                ->getOptionLabelFromRecordUsing(fn (Island $record): string => self::islandOptionLabel($record))
                ->required()
                ->searchable(['name', 'type'])
                ->preload()
                ->native(false)
                ->helperText('Games are available on Horror Island only.'),

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
            Tables\Columns\TextColumn::make('island.name')
                ->label('Island')
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

    private static function islandOptionLabel(Island $island): string
    {
        return $island->name;
    }
}
