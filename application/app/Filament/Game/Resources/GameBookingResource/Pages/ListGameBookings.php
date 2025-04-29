<?php

namespace App\Filament\Game\Resources\GameBookingResource\Pages;

use App\Filament\Game\Resources\GameBookingResource;
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
