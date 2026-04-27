<?php

namespace App\Filament;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Carbon;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('startDate')
                    ->label('From')
                    ->default(Carbon::now()->startOfMonth()->toDateString())
                    ->native(false)
                    ->closeOnDateSelection(),
                DatePicker::make('endDate')
                    ->label('To')
                    ->default(Carbon::now()->toDateString())
                    ->native(false)
                    ->closeOnDateSelection(),
            ])
            ->columns([
                'default' => 1,
                'md' => 2,
            ]);
    }
}
