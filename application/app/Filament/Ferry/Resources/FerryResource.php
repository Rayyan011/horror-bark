<?php

namespace App\Filament\Ferry\Resources;


use App\Filament\Resources\FerryResource\Pages;
use App\Models\Ferry;
use App\Models\User;
use App\Models\Island;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FerryResource extends Resource
{
    protected static ?string $model = Ferry::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Select::make('user_id')
                    ->relationship('owner', 'name')
                    ->required()
                    ->label('Owner'),

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
                Tables\Columns\TextColumn::make('owner.name')->label('Owner'),
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

    // ➡️ This is the important part to restrict what each Ferry Operator can see
    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->where('user_id', Auth::id());
    // }

    // public static function canEdit($record): bool
    // {
    //     return $record->user_id === Auth::id();
    // }

    // public static function canDelete($record): bool
    // {
    //     return $record->user_id === Auth::id();
    // }

    // public static function canView($record): bool
    // {
    //     return $record->user_id === Auth::id();
    // }
}
