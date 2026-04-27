<?php

namespace App\Filament\Hotel\Resources;

use App\Filament\Hotel\Resources\RoomResource\Pages;
use App\Models\Hotel;
use App\Models\Room;
use App\Support\AdminImage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Rooms';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('hotel', fn (Builder $query) => $query->where('user_id', auth()->id()));
    }

    public static function ownedHotelOptions(): array
    {
        return Hotel::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function ownedRoomOptions(?int $hotelId): array
    {
        if (! $hotelId) {
            return [];
        }

        return Room::query()
            ->where('hotel_id', $hotelId)
            ->whereHas('hotel', fn (Builder $query) => $query->where('user_id', auth()->id()))
            ->orderBy('room_number')
            ->pluck('room_number', 'id')
            ->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('hotel_id')
                ->label('Hotel')
                ->options(fn () => self::ownedHotelOptions())
                ->searchable()
                ->preload()
                ->native(false)
                ->rules([self::ownedHotelValidationRule()])
                ->required(),

            TextInput::make('room_number')
                ->label('Room')
                ->required(),

            TextInput::make('price')
                ->numeric()
                ->prefix('MVR')
                ->required(),

            TextInput::make('max_occupancy')
                ->numeric()
                ->required(),

            TextInput::make('status')
                ->default('available')
                ->maxLength(50),

            MarkdownEditor::make('description')
                ->columnSpanFull(),

            FileUpload::make('images')
                ->label('Room Images')
                ->directory('rooms/gallery')
                ->multiple()
                ->maxFiles(3)
                ->image()
                ->imageEditor()
                ->reorderable()
                ->appendFiles(),

            TagsInput::make('amenities')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Hotel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('MVR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_occupancy')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('images')
                    ->getStateUsing(fn ($record) => AdminImage::first($record->images))
                    ->size(50)
                    ->label('Gallery'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    private static function ownedHotelValidationRule(): \Closure
    {
        return function () {
            return function (string $attribute, $value, $fail): void {
                $ownsHotel = Hotel::query()
                    ->whereKey($value)
                    ->where('user_id', auth()->id())
                    ->exists();

                if (! $ownsHotel) {
                    $fail('Select one of your hotels.');
                }
            };
        };
    }
}
