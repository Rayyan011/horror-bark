<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FerryBookingResource\Pages;
use App\Models\FerryBooking;
use App\Models\Ferry;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\HasBookingBulkActions;
use App\Filament\Concerns\HasBookingExport;
use Illuminate\Database\Eloquent\Builder;

class FerryBookingResource extends Resource
{
    use HasBookingBulkActions, HasBookingExport;
    protected static ?string $model = FerryBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Select::make('ferry_id')
                    ->relationship('ferry', 'name')
                    ->required(),

                DateTimePicker::make('booking_time')
                ->required()
                ->label('Booking Time'),   //add validation between for booking between 9am and 4pm

                TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                TextInput::make('total_price')
                    ->numeric()
                    ->required()
                    ->prefix('MVR'),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('ferry.name')->label('Ferry'),
                Tables\Columns\TextColumn::make('booking_time')->label('Booking Time')->dateTime('Y-m-d H:i'),
                Tables\Columns\TextColumn::make('quantity')->label('Qty'),
                Tables\Columns\TextColumn::make('total_price')->money('MVR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'canceled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ]),
            ])
            ->headerActions([
                static::getExportHeaderAction(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ...static::getBookingBulkActions(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    protected static function getExportColumns(): array
    {
        return [
            'ID' => 'id',
            'Customer' => fn ($r) => $r->user?->name ?? 'N/A',
            'Ferry' => fn ($r) => $r->ferry?->name ?? 'N/A',
            'Booking Time' => fn ($r) => $r->booking_time,
            'Quantity' => 'quantity',
            'Total Price' => 'total_price',
            'Status' => 'status',
            'Created At' => fn ($r) => $r->created_at?->toDateTimeString(),
        ];
    }

    protected static function getExportRelations(): array
    {
        return ['user', 'ferry'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFerryBookings::route('/'),
            'create' => Pages\CreateFerryBooking::route('/create'),
            'edit' => Pages\EditFerryBooking::route('/{record}/edit'),
        ];
    }
}
