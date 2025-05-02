<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\FerryBookingResource\Pages;
use App\Models\Ferry;
use App\Models\FerryBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

class FerryBookingResource extends Resource
{
    protected static ?string $model = FerryBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('user_id')
                ->default(fn () => auth()->id())
                ->required(),

            Select::make('ferry_id')
                ->label('Ferry')
                ->options(Ferry::pluck('name', 'id')->toArray())
                ->required(),

            DateTimePicker::make('booking_time')
                ->label('Booking Time')
                ->required()
                ->native(false)
                ->displayFormat('Y-m-d H:i')
                ->rules([
                    function () {
                        return function (string $attribute, $value, $fail) {
                            $time = date('H:i', strtotime($value));
                            if ($time < '09:00' || $time > '16:00') {
                                $fail('Booking time must be between 9:00 and 16:00.');
                            }
                        };
                    },
                ]),

            TextInput::make('quantity')
                ->numeric()
                ->minValue(1)
                ->required(),

            TextInput::make('total_price')
                ->numeric()
                ->prefix('MVR')
                ->required(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'cancelled' => 'Cancelled',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('ferry.name')->label('Ferry'),
            Tables\Columns\TextColumn::make('booking_time')->dateTime(),
            Tables\Columns\TextColumn::make('quantity'),
            Tables\Columns\TextColumn::make('total_price'),
            Tables\Columns\TextColumn::make('status')->sortable(),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
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
