<?php

namespace App\Filament\Ferry\Widgets;

use App\Models\FerryBooking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Carbon;

class FerryTodayDeparturesTable extends TableWidget
{
    protected static ?int $sort = 5;

    protected function getTableHeading(): ?string
    {
        return 'Upcoming Departures Today';
    }

    public function table(Table $table): Table
    {
        $ownerId = auth()->id();
        $todayStart = Carbon::now()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();

        return $table
            ->query(
                FerryBooking::query()
                    ->whereHas('ferry', fn ($query) => $query->where('user_id', $ownerId))
                    ->where('status', '!=', 'canceled')
                    ->where('booking_time', '>=', $todayStart)
                    ->where('booking_time', '<', $tomorrowStart)
                    ->with('ferry')
                    ->orderBy('booking_time')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_time')
                    ->label('Time')
                    ->dateTime('H:i'),
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
