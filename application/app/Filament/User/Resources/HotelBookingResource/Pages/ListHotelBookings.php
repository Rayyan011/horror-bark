<?php

namespace App\Filament\User\Resources\HotelBookingResource\Pages;

use App\Filament\User\Resources\HotelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelBookings extends ListRecords
{
    protected static string $resource = HotelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
