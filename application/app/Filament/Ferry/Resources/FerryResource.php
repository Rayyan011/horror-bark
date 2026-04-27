<?php

namespace App\Filament\Ferry\Resources;

use App\Filament\Ferry\Resources\FerryResource\Pages;
use App\Models\Ferry;
use App\Support\AdminImage;
use App\Support\HorrorDistrictCatalog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FerryResource extends Resource
{
    protected static ?string $model = Ferry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->required(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                \Filament\Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),

                Select::make('island_id')
                    ->relationship('island', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->label('Island'),

                Select::make('location')
                    ->label('District')
                    ->options(HorrorDistrictCatalog::allLocations())
                    ->searchable()
                    ->preload()
                    ->native(false),

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

                FileUpload::make('images')
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
                Tables\Columns\TextColumn::make('location')->label('District'),
                Tables\Columns\TextColumn::make('price')->money('MVR')->sortable(),
                Tables\Columns\TextColumn::make('max_capacity')->sortable(),
                Tables\Columns\TextColumn::make('max_booking_quantity')->sortable(),
                Tables\Columns\ImageColumn::make('images')
                    ->getStateUsing(fn ($record) => AdminImage::first($record->images))
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
            //
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
}
