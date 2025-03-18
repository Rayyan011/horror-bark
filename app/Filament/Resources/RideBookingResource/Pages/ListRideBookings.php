<?php

namespace App\Filament\Resources\RideBookingResource\Pages;

use App\Filament\Resources\RideBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRideBookings extends ListRecords
{
    protected static string $resource = RideBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
