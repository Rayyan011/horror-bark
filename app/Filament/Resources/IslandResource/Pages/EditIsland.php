<?php

namespace App\Filament\Resources\IslandResource\Pages;

use App\Filament\Resources\IslandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIsland extends EditRecord
{
    protected static string $resource = IslandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
