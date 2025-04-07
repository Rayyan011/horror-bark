<?php

namespace App\Filament\Resources\FerrySlotResource\Pages;

use App\Filament\Resources\FerrySlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFerrySlots extends ListRecords
{
    protected static string $resource = FerrySlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
