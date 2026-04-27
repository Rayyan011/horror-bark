<?php

namespace App\Filament\Ride\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\RideBooking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RideTopRidesTable extends TableWidget
{
    use HasDashboardDateRange;

    protected static ?int $sort = 4;

    protected function getTableHeading(): ?string
    {
        return 'Top Rides (Selected Range)';
    }

    public function table(Table $table): Table
    {
        $ownerId = auth()->id();
        [$start, $end] = $this->getDashboardDateRange();

        return $table
            ->query(
                RideBooking::query()
                    ->selectRaw('ride_id, SUM(quantity) as booked_quantity, SUM(total_price) as revenue')
                    ->whereHas('ride', fn ($query) => $query->where('user_id', $ownerId))
                    ->where('status', '!=', 'canceled')
                    ->where('booking_time', '>=', $start)
                    ->where('booking_time', '<', $end)
                    ->groupBy('ride_id')
                    ->orderByDesc('booked_quantity')
                    ->limit(5)
                    ->with('ride')
            )
            ->columns([
                Tables\Columns\TextColumn::make('ride.name')
                    ->label('Ride')
                    ->sortable(),
                Tables\Columns\TextColumn::make('booked_quantity')
                    ->label('Tickets')
                    ->sortable(),
                Tables\Columns\TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('MVR')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
