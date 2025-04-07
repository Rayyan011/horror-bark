<?php

namespace App\Filament\Resources\BeachEventResource\Pages;

use App\Filament\Resources\BeachEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBeachEvents extends ListRecords
{
    protected static string $resource = BeachEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
