<?php

namespace App\Filament\Hotel\Resources\HotelBookingResource\Pages;

use App\Filament\Hotel\Resources\HotelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotelBooking extends EditRecord
{
    protected static string $resource = HotelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
