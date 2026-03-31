<?php

namespace App\Services;

use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\Island;
use App\Models\RideBooking;
use App\Models\User;
use App\Support\BookingSupport;
use Illuminate\Support\Collection;

class BookingReportService
{
    public function adminRows(array $filters): Collection
    {
        $selectedType = $filters['type'] ?? null;
        $types = $selectedType ? [$selectedType] : ['hotel', 'ferry', 'ride', 'game', 'beach-event'];

        return collect($types)
            ->flatMap(fn (string $type) => $this->rowsForType($type, $filters))
            ->sortByDesc('start_at')
            ->values();
    }

    public function operatorRows(User $user, string $domain, array $filters): Collection
    {
        return $this->rowsForType($domain, $filters, $user)
            ->sortByDesc('start_at')
            ->values();
    }

    public function summary(Collection $rows): array
    {
        return [
            'bookings' => $rows->count(),
            'passengers' => (int) $rows->sum('quantity'),
            'revenue' => (float) $rows->where('status', '!=', 'canceled')->sum('total_price'),
            'cancellations' => $rows->where('status', 'canceled')->count(),
        ];
    }

    public function islands(): Collection
    {
        return Island::query()->orderBy('name')->get(['id', 'name']);
    }

    public function exportRows(Collection $rows): array
    {
        return $rows->map(fn (array $row) => [
            $row['type_label'],
            $row['title'],
            $row['customer_name'],
            $row['customer_email'],
            $row['island_name'],
            $row['schedule'],
            $row['quantity'],
            number_format((float) $row['total_price'], 2, '.', ''),
            $row['status'],
            $row['invoice_number'],
            $row['pass_number'],
        ])->all();
    }

    public function headings(): array
    {
        return [
            'type',
            'listing',
            'customer_name',
            'customer_email',
            'island',
            'schedule',
            'quantity',
            'total_price',
            'status',
            'invoice_number',
            'pass_number',
        ];
    }

    private function rowsForType(string $type, array $filters, ?User $owner = null): Collection
    {
        return match ($type) {
            'hotel' => $this->hotelRows($filters, $owner),
            'ferry' => $this->ferryRows($filters, $owner),
            'ride' => $this->rideRows($filters, $owner),
            'game' => $this->gameRows($filters, $owner),
            'beach-event' => $this->beachEventRows($filters),
            default => collect(),
        };
    }

    private function hotelRows(array $filters, ?User $owner = null): Collection
    {
        $query = HotelBooking::query()->with(['room.hotel', 'user', 'invoice']);

        if ($owner) {
            $query->whereHas('room.hotel', fn ($builder) => $builder->where('user_id', $owner->id));
        }

        $this->applyStatusAndDateFilters($query, $filters, 'start_date');

        return $query->get()->map(fn (HotelBooking $booking) => $this->mapRow($booking));
    }

    private function ferryRows(array $filters, ?User $owner = null): Collection
    {
        $query = FerryBooking::query()->with(['ferry.island', 'user', 'invoice']);

        if ($owner) {
            $query->whereHas('ferry', fn ($builder) => $builder->where('user_id', $owner->id));
        }

        $this->applyStatusAndDateFilters($query, $filters, 'booking_time');

        if (! empty($filters['island_id'])) {
            $query->whereHas('ferry', fn ($builder) => $builder->where('island_id', $filters['island_id']));
        }

        return $query->get()->map(fn (FerryBooking $booking) => $this->mapRow($booking));
    }

    private function rideRows(array $filters, ?User $owner = null): Collection
    {
        $query = RideBooking::query()->with(['ride.island', 'user', 'invoice']);

        if ($owner) {
            $query->whereHas('ride', fn ($builder) => $builder->where('user_id', $owner->id));
        }

        $this->applyStatusAndDateFilters($query, $filters, 'booking_time');

        if (! empty($filters['island_id'])) {
            $query->whereHas('ride', fn ($builder) => $builder->where('island_id', $filters['island_id']));
        }

        return $query->get()->map(fn (RideBooking $booking) => $this->mapRow($booking));
    }

    private function gameRows(array $filters, ?User $owner = null): Collection
    {
        $query = GameBooking::query()->with(['game.island', 'user', 'invoice']);

        if ($owner) {
            $query->whereHas('game', fn ($builder) => $builder->where('user_id', $owner->id));
        }

        $this->applyStatusAndDateFilters($query, $filters, 'booking_time');

        if (! empty($filters['island_id'])) {
            $query->whereHas('game', fn ($builder) => $builder->where('island_id', $filters['island_id']));
        }

        return $query->get()->map(fn (GameBooking $booking) => $this->mapRow($booking));
    }

    private function beachEventRows(array $filters): Collection
    {
        $query = BeachEventBooking::query()->with(['beachEvent.island', 'user', 'invoice']);
        $this->applyStatusAndDateFilters($query, $filters, 'booking_date');

        if (! empty($filters['island_id'])) {
            $query->whereHas('beachEvent', fn ($builder) => $builder->where('island_id', $filters['island_id']));
        }

        return $query->get()->map(fn (BeachEventBooking $booking) => $this->mapRow($booking));
    }

    private function applyStatusAndDateFilters($query, array $filters, string $dateColumn): void
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['from'])) {
            $query->whereDate($dateColumn, '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate($dateColumn, '<=', $filters['to']);
        }
    }

    private function mapRow($booking): array
    {
        return [
            'id' => $booking->id,
            'type' => BookingSupport::typeKey($booking),
            'type_label' => BookingSupport::typeLabel($booking),
            'title' => BookingSupport::title($booking),
            'customer_name' => BookingSupport::customerName($booking),
            'customer_email' => $booking->user?->email,
            'island_name' => BookingSupport::islandName($booking),
            'schedule' => BookingSupport::scheduleLabel($booking),
            'start_at' => BookingSupport::startAt($booking),
            'quantity' => (int) $booking->quantity,
            'total_price' => (float) $booking->total_price,
            'status' => $booking->status,
            'invoice_number' => $booking->invoice?->invoice_number,
            'pass_number' => $booking->pass_number ?? null,
        ];
    }
}
