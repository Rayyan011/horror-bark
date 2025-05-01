<?php

namespace App\Filament\User\Resources\FerryBookingResource\Pages;

use App\Filament\User\Resources\FerryBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFerryBookings extends ListRecords
{
    protected static string $resource = FerryBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
