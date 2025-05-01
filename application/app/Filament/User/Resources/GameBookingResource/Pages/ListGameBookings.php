<?php

namespace App\Filament\User\Resources\GameBookingResource\Pages;

use App\Filament\User\Resources\GameBookingResource;
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
