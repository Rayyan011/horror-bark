<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FerryResource\Pages;
use App\Models\Ferry;
use App\Models\Island;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FerryResource extends Resource
{
    protected static ?string $model = Ferry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('owner', 'name')
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->native(false)
                    ->required()
                    ->label('Owner'),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('MVR'),

                TextInput::make('max_capacity')
                    ->numeric()
                    ->required(),

                TextInput::make('max_booking_quantity')
                    ->numeric()
                    ->required(),

                Select::make('island_id')
                    ->relationship('island', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Island $record): string => self::islandOptionLabel($record))
                    ->searchable(['name', 'type'])
                    ->preload()
                    ->native(false)
                    ->required()
                    ->label('Home Island'),

                Forms\Components\FileUpload::make('images')
                    ->label('Additional Images')
                    ->directory('ferries/gallery')
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
                Tables\Columns\TextColumn::make('name')->sortable(),
                Tables\Columns\TextColumn::make('owner.name')->label('Owner'),
                Tables\Columns\TextColumn::make('island.name')->label('Island'),
                Tables\Columns\TextColumn::make('price')->money('MVR')->sortable(),
                Tables\Columns\TextColumn::make('max_capacity')->sortable(),
                Tables\Columns\TextColumn::make('max_booking_quantity')->sortable(),
                Tables\Columns\ImageColumn::make('images')
                    ->disk('public')
                    ->getStateUsing(fn ($record) => $record->images[0] ?? null)
                    ->size(50)
                    ->label('Gallery'),
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
            // If you want to manage bookings directly from the Ferry Resource:
            // RelationManagers\BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFerries::route('/'),
            'create' => Pages\CreateFerry::route('/create'),
            'edit' => Pages\EditFerry::route('/{record}/edit'),
        ];
    }

    private static function islandOptionLabel(Island $island): string
    {
        return $island->name;
    }
}
