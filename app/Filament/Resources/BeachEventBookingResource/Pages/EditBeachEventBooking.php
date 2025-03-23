<?php

namespace App\Filament\Resources\BeachEventBookingResource\Pages;

use App\Filament\Resources\BeachEventBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBeachEventBooking extends EditRecord
{
    protected static string $resource = BeachEventBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
