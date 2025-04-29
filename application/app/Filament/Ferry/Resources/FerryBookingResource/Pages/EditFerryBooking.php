<?php

namespace App\Filament\Ferry\Resources\FerryBookingResource\Pages;

use App\Filament\Ferry\Resources\FerryBookingResource;
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
