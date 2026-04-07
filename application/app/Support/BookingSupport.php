<?php

namespace App\Support;

use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\Invoice;
use App\Models\RideBooking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BookingSupport
{
    public const SELF_SERVICE_CUTOFF_HOURS = 24;

    public static function typeKey(Model $booking): string
    {
        return match (true) {
            $booking instanceof HotelBooking => 'hotel',
            $booking instanceof FerryBooking => 'ferry',
            $booking instanceof RideBooking => 'ride',
            $booking instanceof GameBooking => 'game',
            $booking instanceof BeachEventBooking => 'beach-event',
            default => 'booking',
        };
    }

    public static function typeLabel(Model $booking): string
    {
        return match (true) {
            $booking instanceof HotelBooking => 'Hotel',
            $booking instanceof FerryBooking => 'Ferry',
            $booking instanceof RideBooking => 'Ride',
            $booking instanceof GameBooking => 'Game',
            $booking instanceof BeachEventBooking => 'Beach Event',
            default => 'Booking',
        };
    }

    public static function startAt(Model $booking): Carbon
    {
        return match (true) {
            $booking instanceof HotelBooking => Carbon::parse($booking->start_date),
            $booking instanceof BeachEventBooking => Carbon::parse(
                Carbon::parse($booking->booking_date)->toDateString().' '.$booking->booking_time?->format('H:i:s')
            ),
            default => Carbon::parse($booking->booking_time),
        };
    }

    public static function cutoffAt(Model $booking): Carbon
    {
        return static::startAt($booking)->copy()->subHours(static::SELF_SERVICE_CUTOFF_HOURS);
    }

    public static function canSelfServiceChange(Model $booking): bool
    {
        return $booking->status === 'confirmed' && now()->lt(static::cutoffAt($booking));
    }

    public static function scheduleLabel(Model $booking): string
    {
        return match (true) {
            $booking instanceof HotelBooking => Carbon::parse($booking->start_date)->toDateString().' -> '.Carbon::parse($booking->end_date)->toDateString(),
            $booking instanceof BeachEventBooking => Carbon::parse($booking->booking_date)->toDateString().' '.static::startAt($booking)->format('H:i'),
            default => static::startAt($booking)->format('Y-m-d H:i'),
        };
    }

    public static function title(Model $booking): string
    {
        return match (true) {
            $booking instanceof HotelBooking => $booking->room?->hotel?->name ?? 'Hotel',
            $booking instanceof FerryBooking => $booking->ferry?->name ?? 'Ferry',
            $booking instanceof RideBooking => $booking->ride?->name ?? 'Ride',
            $booking instanceof GameBooking => $booking->game?->name ?? 'Game',
            $booking instanceof BeachEventBooking => $booking->beachEvent?->name ?? 'Beach Event',
            default => 'Booking',
        };
    }

    public static function islandName(Model $booking): ?string
    {
        return match (true) {
            $booking instanceof HotelBooking => null,
            $booking instanceof FerryBooking => $booking->ferry?->island?->name,
            $booking instanceof RideBooking => $booking->ride?->island?->name,
            $booking instanceof GameBooking => $booking->game?->island?->name,
            $booking instanceof BeachEventBooking => $booking->beachEvent?->island?->name,
            default => null,
        };
    }

    public static function customerName(Model $booking): string
    {
        return $booking->user?->name ?? 'Unknown customer';
    }

    public static function invoiceDownloadUrl(?Invoice $invoice): ?string
    {
        return $invoice ? route('invoices.download', $invoice) : null;
    }

    public static function passDownloadUrl(Model $booking): ?string
    {
        return $booking instanceof FerryBooking ? route('bookings.ferries.pass', $booking) : null;
    }
}
