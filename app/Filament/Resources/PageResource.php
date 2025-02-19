<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pages';

    protected static ?string $navigationGroup = 'Website Configurations';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('page_name')->columnSpanFull()
                    ->label('Page Name'),

                Builder::make('content')
                    ->label("Page Content")
                    ->columnSpanFull()
                    ->blocks([
                        Builder\Block::make('heading')
                            ->schema([
                                TextInput::make('pos')
                                    ->label('Heading Position')
                                    ->required(),
                                TextInput::make('content')
                                    ->label('Heading')
                                    ->required(),
                            ])
                            ->columns(2),
                        Builder\Block::make('text')
                            ->schema([
                                TextInput::make('pos')
                                    ->label('Text Position')
                                    ->required(),
                                Textarea::make('content')
                                    ->label('Text')
                                    ->required(),
                            ]),

                        Builder\Block::make('image')
                            ->schema([
                                TextInput::make('pos')
                                    ->label('Image Position')
                                    ->required(),
                                FileUpload::make('content')
                                    ->label('Image')
                                    ->image()
                                    ->required(),
                            ]),

                        Builder\Block::make('icon')
                            ->schema([
                                TextInput::make('pos')
                                    ->label('Icon Position')
                                    ->required(),
                                TextInput::make('content')
                                    ->label('Icon')
                                    ->required(),
                            ]),

                        Builder\Block::make('imageset')
                            ->schema([
                                TextInput::make('pos')
                                    ->label('Imageset  Position')
                                    ->required(),
                                FileUpload::make('content')
                                    ->label('Imageset')
                                    ->image()
                                    ->multiple()
                                    ->imageEditor()
                                    ->reorderable()
                                    ->appendFiles()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->required(),
                            ]),

                        Builder\Block::make('options')
                            ->schema([
                                Select::make('options')
                                    ->options([
                                        '1',
                                        '2',
                                    ])
                                    ->required(),
                            ]),
                        Builder\Block::make('options')
                            ->schema([
                                Select::make('options')
                                    ->options([
                                        '1',
                                        '2',
                                    ])
                                    ->required(),
                            ]),
                            // Builder\Block::make('MarkdownEditor')
                            // ->schema([
                            //     TextInput::make('pos')
                            //         ->label('Markdown Position')
                            //         ->required(),
                            //     MarkdownEditor::make('content')
                            //         ->label('markdown')
                            //         ->required(),
                            // ]),
                        Builder\Block::make('RichEditor')
                            ->schema([
                            TextInput::make('pos')
                                ->label('RichEditor Position')
                                ->required(),
                            RichEditor::make('content')
                                ->toolbarButtons([                            
                                    'bold',
                                    'bulletList',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',                               
                                    'underline',
                                    'undo',
                            ]),
                        ]),                         

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page_name')
                    ->searchable(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}