<?php

namespace App\Filament\User\Resources\GameBookingResource\Pages;

use App\Filament\User\Resources\GameBookingResource;
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
