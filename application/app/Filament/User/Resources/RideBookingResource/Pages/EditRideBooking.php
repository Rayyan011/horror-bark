<?php

namespace App\Filament\User\Resources\RideBookingResource\Pages;

use App\Filament\User\Resources\RideBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRideBooking extends EditRecord
{
    protected static string $resource = RideBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
