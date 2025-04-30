<?php

namespace App\Filament\Hotel\Resources\HotelResource\Pages;

use App\Filament\Hotel\Resources\HotelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotel extends EditRecord
{
    protected static string $resource = HotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
