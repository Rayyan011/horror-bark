<?php

namespace App\Filament\Hotel\Resources;

use App\Filament\Hotel\Resources\HotelResource\Pages;
use App\Models\Hotel;
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

            Forms\Components\TextInput::make('location')
                ->required(),

            Forms\Components\Section::make('Public Map Placement')
                ->schema([
                    Forms\Components\Placeholder::make('horror_map_picker')
                        ->hiddenLabel()
                        ->content(new \Illuminate\Support\HtmlString(view('filament.forms.components.horror-map-picker')->render())),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('map_x')
                                ->label('Map X')
                                ->numeric()
                                ->default(50)
                                ->readOnly()
                                ->extraInputAttributes(['data-horror-map-x' => '1']),
                            Forms\Components\TextInput::make('map_y')
                                ->label('Map Y')
                                ->numeric()
                                ->default(50)
                                ->readOnly()
                                ->extraInputAttributes(['data-horror-map-y' => '1']),
                        ]),
                ])
                ->columnSpanFull(),

            Forms\Components\TextInput::make('latitude')
                ->numeric(),

            Forms\Components\TextInput::make('longitude')
                ->numeric(),

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
            Tables\Columns\TextColumn::make('latitude')->sortable(),
            Tables\Columns\TextColumn::make('longitude')->sortable(),
            Tables\Columns\ImageColumn::make('images')
                ->disk('public')
                ->getStateUsing(fn ($record) => $record->images[0] ?? null)
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
