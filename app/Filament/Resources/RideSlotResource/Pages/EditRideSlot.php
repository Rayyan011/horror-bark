<?php

namespace App\Filament\Resources\RideSlotResource\Pages;

use App\Filament\Resources\RideSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRideSlot extends EditRecord
{
    protected static string $resource = RideSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
