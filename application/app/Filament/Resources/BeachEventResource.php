<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeachEventResource\Pages;
use App\Filament\Resources\BeachEventResource\RelationManagers;
use App\Models\BeachEvent;
use App\Models\Island;
use App\Services\IslandAccessService;
use App\Support\AdminImage;
use App\Support\HorrorDistrictCatalog;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BeachEventResource extends Resource
{
    protected static ?string $model = BeachEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            // Organizer (owner) selection
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
            Forms\Components\Select::make('island_id')
                ->label('Island')
                ->relationship('island', 'name', fn ($query) => $query->where('type', IslandAccessService::PICNIC_ISLAND))
                ->getOptionLabelFromRecordUsing(fn (Island $record): string => self::islandOptionLabel($record))
                ->required()
                ->searchable(['name', 'type'])
                ->preload()
                ->native(false)
                ->helperText('Beach events are available on Picnic Island only.'),

            Forms\Components\Select::make('location')
                ->label('District')
                ->options(HorrorDistrictCatalog::picnicLocations())
                ->searchable()
                ->preload()
                ->native(false),

            // File Upload for Images
            Forms\Components\FileUpload::make('images')
                ->label('Additional Images')
                ->directory('beach-events/gallery')
                ->multiple()
                ->maxFiles(5)
                ->image()
                ->maxSize(1024),
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
            Tables\Columns\TextColumn::make('location')->label('District'),
            Tables\Columns\ImageColumn::make('images')
                ->getStateUsing(fn ($record) => AdminImage::first($record->images))
                ->size(50)
                ->label('Gallery'),
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

    private static function islandOptionLabel(Island $island): string
    {
        return $island->name;
    }
}
