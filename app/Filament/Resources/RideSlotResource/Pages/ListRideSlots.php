<?php

namespace App\Filament\Resources\RideSlotResource\Pages;

use App\Filament\Resources\RideSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRideSlots extends ListRecords
{
    protected static string $resource = RideSlotResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
