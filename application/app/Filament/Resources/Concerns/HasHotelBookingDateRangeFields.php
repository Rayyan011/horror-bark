<?php

namespace App\Filament\Resources\Concerns;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;

trait HasHotelBookingDateRangeFields
{
    protected static function hotelBookingDateRangeFields(callable $recalculatePrice): array
    {
        return [
            DatePicker::make('start_date')
                ->label('From')
                ->native(false)
                ->minDate(Carbon::today())
                ->closeOnDateSelection()
                ->live()
                ->rules(['after_or_equal:today'])
                ->validationMessages([
                    'after_or_equal' => 'Choose today or a future check-in date.',
                ])
                ->afterStateUpdated(function (Get $get, callable $set) use ($recalculatePrice): void {
                    $startDate = $get('start_date');
                    $endDate = $get('end_date');

                    if ($startDate && $endDate && Carbon::parse($endDate)->lte(Carbon::parse($startDate))) {
                        $set('end_date', null);
                    }

                    $recalculatePrice(fn (string $key) => $get($key), $set);
                })
                ->required(),

            DatePicker::make('end_date')
                ->label('To')
                ->native(false)
                ->minDate(fn (Get $get): string => self::minimumHotelCheckoutDate($get('start_date')))
                ->closeOnDateSelection()
                ->live()
                ->rules(['after:start_date'])
                ->validationMessages([
                    'after' => 'Choose a check-out date after check-in.',
                ])
                ->afterStateUpdated(fn (Get $get, callable $set) => $recalculatePrice(fn (string $key) => $get($key), $set))
                ->required(),
        ];
    }

    private static function minimumHotelCheckoutDate(?string $startDate): string
    {
        if (blank($startDate)) {
            return Carbon::tomorrow()->toDateString();
        }

        return Carbon::parse($startDate)->addDay()->toDateString();
    }
}
