<?php

namespace App\Filament\Ferry\Resources\FerryResource\Pages;

use App\Filament\Ferry\Resources\FerryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFerries extends ListRecords
{
    protected static string $resource = FerryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
