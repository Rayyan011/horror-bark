<?php

namespace App\Filament\Resources\GameBookingResource\Pages;

use App\Filament\Resources\GameBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameBookings extends ListRecords
{
    protected static string $resource = GameBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
