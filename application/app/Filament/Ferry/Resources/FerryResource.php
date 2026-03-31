<?php

namespace App\Filament\Ferry\Resources;

use App\Filament\Ferry\Resources\FerryResource\Pages;
use App\Models\Ferry;
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

                Select::make('island_id')
                    ->relationship('island', 'name')
                    ->required()
                    ->label('Island'),

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable(),
                Tables\Columns\TextColumn::make('island.name')->label('Island'),
                Tables\Columns\TextColumn::make('price')->money('MVR')->sortable(),
                Tables\Columns\TextColumn::make('max_capacity')->sortable(),
                Tables\Columns\TextColumn::make('max_booking_quantity')->sortable(),
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
