<?php

namespace App\Filament\Resources\BeachEventBookingResource\Pages;

use App\Filament\Resources\BeachEventBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBeachEventBookings extends ListRecords
{
    protected static string $resource = BeachEventBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
