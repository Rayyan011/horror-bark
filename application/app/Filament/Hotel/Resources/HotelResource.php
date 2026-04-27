<?php

namespace App\Filament\Hotel\Resources;

use App\Filament\Hotel\Resources\HotelResource\Pages;
use App\Models\Hotel;
use App\Support\AdminImage;
use App\Support\HorrorDistrictCatalog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Hidden;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

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
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\Textarea::make('description')
                ->rows(4)
                ->columnSpanFull(),

            Forms\Components\Select::make('location')
                ->label('District / Location')
                ->options(HorrorDistrictCatalog::hotelLocations())
                ->searchable()
                ->preload()
                ->native(false)
                ->helperText('Hotels stay on Horror Island; this selects the district shown publicly.'),

            Forms\Components\FileUpload::make('images')
                ->label('Gallery Images')
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
            Tables\Columns\TextColumn::make('name')->sortable(),
            Tables\Columns\TextColumn::make('location')->sortable(),
            Tables\Columns\ImageColumn::make('images')
                ->getStateUsing(fn ($record) => AdminImage::first($record->images))
                ->size(50)
                ->label('Image'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
