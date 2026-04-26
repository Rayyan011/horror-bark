<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelResource\Pages;
use App\Models\Hotel;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                ->preload()
                ->native(false)
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
