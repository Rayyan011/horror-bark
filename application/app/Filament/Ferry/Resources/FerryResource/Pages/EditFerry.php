<?php

namespace App\Filament\Ferry\Resources\FerryResource\Pages;

use App\Filament\Ferry\Resources\FerryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFerry extends EditRecord
{
    protected static string $resource = FerryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
