<?php

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

trait HasBookingBulkActions
{
    protected static function getBookingBulkActions(): array
    {
        return [
            BulkAction::make('confirm')
                ->label('Confirm')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion()
                ->action(function (Collection $records) {
                    $count = 0;
                    foreach ($records as $record) {
                        if ($record->status !== 'confirmed') {
                            $record->update(['status' => 'confirmed']);
                            $count++;
                        }
                    }
                    Notification::make()
                        ->title("{$count} booking(s) confirmed")
                        ->success()
                        ->send();
                }),

            BulkAction::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion()
                ->action(function (Collection $records) {
                    $count = 0;
                    foreach ($records as $record) {
                        if ($record->status !== 'canceled') {
                            $record->update(['status' => 'canceled']);
                            if (method_exists($record, 'invoice') && $record->invoice) {
                                $record->invoice->update(['status' => 'canceled']);
                            }
                            $count++;
                        }
                    }
                    Notification::make()
                        ->title("{$count} booking(s) canceled")
                        ->warning()
                        ->send();
                }),
        ];
    }
}
