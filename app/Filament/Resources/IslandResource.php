<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IslandResource\Pages;
use App\Filament\Resources\IslandResource\RelationManagers;
use App\Models\Island;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;


class IslandResource extends Resource
{
    protected static ?string $model = Island::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                Select::make('type')
                    ->options([
                        'Horror-Island' => 'Horror Island',
                        'Picnic-Island' => 'Picnic Island'
                    ]),
                MarkdownEditor::make('description')


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->label('Type')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Name')->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Description')->sortable()
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
            'index' => Pages\ListIslands::route('/'),
            'create' => Pages\CreateIsland::route('/create'),
            'edit' => Pages\EditIsland::route('/{record}/edit'),
        ];
    }
}
