<?php

namespace App\Filament\Concerns;

use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HasBookingExport
{
    protected static function getExportHeaderAction(): Action
    {
        return Action::make('export')
            ->label('Export CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function () {
                $query = static::getEloquentQuery();
                $model = static::getModel();
                $filename = str_replace('\\', '_', class_basename($model)) . '_' . now()->format('Y-m-d') . '.csv';

                return new StreamedResponse(function () use ($query, $model) {
                    $handle = fopen('php://output', 'w');

                    $columns = static::getExportColumns();
                    fputcsv($handle, array_keys($columns));

                    $query->with(static::getExportRelations())->lazy(200)->each(function ($record) use ($handle, $columns) {
                        $row = [];
                        foreach ($columns as $callback) {
                            $row[] = is_callable($callback) ? $callback($record) : data_get($record, $callback);
                        }
                        fputcsv($handle, $row);
                    });

                    fclose($handle);
                }, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]);
            });
    }

    protected static function getExportColumns(): array
    {
        return [
            'ID' => 'id',
            'Customer' => fn ($r) => $r->user?->name ?? 'N/A',
            'Quantity' => 'quantity',
            'Total Price' => 'total_price',
            'Status' => 'status',
            'Created At' => fn ($r) => $r->created_at?->toDateTimeString(),
        ];
    }

    protected static function getExportRelations(): array
    {
        return ['user'];
    }
}
