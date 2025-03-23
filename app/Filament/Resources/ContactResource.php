<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages; // Import Pages namespace
use App\Models\Contact;
use Filament\Forms; // Import Forms namespace
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables; // Import Tables namespace
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-mail';

    protected static ?string $navigationGroup = 'Customer Service';

    protected static ?string $navigationLabel = 'Contact Inquiries';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name') // Use Forms\Components\TextInput
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name') // Use Forms\Components\TextInput
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email') // Use Forms\Components\TextInput
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number') // Use Forms\Components\TextInput
                    ->tel()
                    ->maxLength(20),
                Forms\Components\Textarea::make('message') // Use Forms\Components\Textarea
                    ->required()
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name'), // Use Tables\Columns\TextColumn
                Tables\Columns\TextColumn::make('last_name'), // Use Tables\Columns\TextColumn
                Tables\Columns\TextColumn::make('email'), // Use Tables\Columns\TextColumn
                Tables\Columns\TextColumn::make('phone_number'), // Use Tables\Columns\TextColumn
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Use Tables\Actions\ViewAction
                Tables\Actions\EditAction::make(), // Use Tables\Actions\EditAction
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([ // Use Tables\Actions\BulkActionGroup
                    Tables\Actions\DeleteBulkAction::make(), // Use Tables\Actions\DeleteBulkAction
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
            'index' => Pages\ListContacts::route('/'), // Use Pages\ListContacts
            'create' => Pages\CreateContact::route('/create'), // Assuming you want a create page - add this if needed
            'view' => Pages\ViewContact::route('/{record}'), // Use Pages\ViewContact
            'edit' => Pages\EditContact::route('/{record}/edit'), // Assuming you want an edit page - add this if needed
        ];
    }
}