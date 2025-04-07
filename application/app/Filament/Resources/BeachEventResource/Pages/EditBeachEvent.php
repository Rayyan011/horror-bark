<?php

namespace App\Filament\Resources\BeachEventResource\Pages;

use App\Filament\Resources\BeachEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBeachEvent extends EditRecord
{
    protected static string $resource = BeachEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
