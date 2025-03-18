<?php

namespace App\Filament\Resources\FerryBookingResource\Pages;

use App\Filament\Resources\FerryBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFerryBooking extends EditRecord
{
    protected static string $resource = FerryBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
