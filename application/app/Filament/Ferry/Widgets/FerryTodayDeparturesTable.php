<?php

namespace App\Filament\Ferry\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\FerryBooking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class FerryTodayDeparturesTable extends TableWidget
{
    use HasDashboardDateRange;

    protected static ?int $sort = 5;

    protected function getTableHeading(): ?string
    {
        return 'Departures In Selected Range';
    }

    public function table(Table $table): Table
    {
        $ownerId = auth()->id();
        [$start, $end] = $this->getDashboardDateRange();

        return $table
            ->query(
                FerryBooking::query()
                    ->whereHas('ferry', fn ($query) => $query->where('user_id', $ownerId))
                    ->where('status', '!=', 'canceled')
                    ->where('booking_time', '>=', $start)
                    ->where('booking_time', '<', $end)
                    ->with('ferry')
                    ->orderBy('booking_time')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_time')
                    ->label('Time')
                    ->dateTime('M j, H:i'),
                Tables\Columns\TextColumn::make('ferry.name')
                    ->label('Ferry'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Passengers'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'canceled',
                    ]),
            ])
            ->paginated(false);
    }
}
