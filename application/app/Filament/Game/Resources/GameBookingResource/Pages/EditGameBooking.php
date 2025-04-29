<?php

namespace App\Filament\Game\Resources\GameBookingResource\Pages;

use App\Filament\Game\Resources\GameBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameBooking extends EditRecord
{
    protected static string $resource = GameBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
