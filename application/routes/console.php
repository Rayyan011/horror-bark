<?php

use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use App\Services\BookingLifecycleService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:send-reminders', function (BookingLifecycleService $bookingLifecycleService) {
    $windowStart = now()->addDay()->startOfHour();
    $windowEnd = $windowStart->copy()->addHour();
    $hotelReminderDate = $windowStart->toDateString();

    foreach ([
        HotelBooking::query()->where('status', 'confirmed')->whereNull('reminder_sent_at')->whereDate('start_date', $hotelReminderDate)->get(),
        FerryBooking::query()->where('status', 'confirmed')->whereNull('reminder_sent_at')->whereBetween('booking_time', [$windowStart, $windowEnd])->get(),
        RideBooking::query()->where('status', 'confirmed')->whereNull('reminder_sent_at')->whereBetween('booking_time', [$windowStart, $windowEnd])->get(),
        GameBooking::query()->where('status', 'confirmed')->whereNull('reminder_sent_at')->whereBetween('booking_time', [$windowStart, $windowEnd])->get(),
        BeachEventBooking::query()->where('status', 'confirmed')->whereNull('reminder_sent_at')->whereBetween('booking_time', [$windowStart, $windowEnd])->get(),
    ] as $group) {
        foreach ($group as $booking) {
            $bookingLifecycleService->sendReminder($booking);
        }
    }
})->purpose('Send 24-hour booking reminder emails');

Schedule::command('bookings:send-reminders')->hourly();
