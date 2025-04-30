<?php

namespace App\Filament\Ride\Resources\RideBookingResource\Pages;

use App\Filament\Ride\Resources\RideBookingResource;
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
