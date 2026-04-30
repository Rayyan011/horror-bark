<?php

namespace Database\Seeders;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\Ride;
use App\Models\Room;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Seeds a populated demo dataset: ~24 customers and ~300 bookings spread across
 * a 60-day window so every dashboard widget — period stats, by-day charts,
 * next-7-days lists, capacity/occupancy meters, ferry departures table — has
 * meaningful data on first load.
 *
 * Booking PDFs (invoices, ferry passes) are intentionally NOT generated here:
 * the InvoiceController and FerryPassController already lazy-generate them on
 * first download, so seeding ~300 PDFs would just waste minutes.
 *
 * Re-run safely: every demo customer (and their bookings + invoices) is wiped
 * and rebuilt. Catalog data (hotels, ferries, etc.) is left alone.
 */
class DemoBookingsSeeder extends Seeder
{
    /** Tune these to grow/shrink the demo dataset. */
    private const CUSTOMER_COUNT = 24;

    private const PAST_WINDOW_DAYS = 30;

    private const FUTURE_WINDOW_DAYS = 30;

    /** Status mix (must sum to 1.0). Past bookings get the past-mix; future the future-mix. */
    private const PAST_STATUS_MIX = ['confirmed' => 0.92, 'canceled' => 0.08];

    private const FUTURE_STATUS_MIX = ['confirmed' => 0.78, 'pending' => 0.16, 'canceled' => 0.06];

    public function run(): void
    {
        if (app()->isProduction() && ! filter_var(env('ALLOW_DEMO_BOOKING_SEED', false), FILTER_VALIDATE_BOOL)) {
            $this->command?->warn('Skipped DemoBookingsSeeder in production. Set ALLOW_DEMO_BOOKING_SEED=true to override.');

            return;
        }

        mt_srand(20260430); // deterministic across runs

        $customerIds = $this->seedCustomers();
        $this->wipeCustomerBookings($customerIds);

        $rooms = Room::query()->get(['id', 'price', 'max_occupancy', 'hotel_id']);
        $ferries = Ferry::query()->get(['id', 'price', 'max_capacity', 'max_booking_quantity']);
        $rides = Ride::query()->get(['id', 'price', 'max_capacity', 'max_booking_quantity']);
        $games = Game::query()->get(['id', 'price', 'max_capacity', 'max_booking_quantity']);
        $beachEvents = BeachEvent::query()->get(['id', 'price', 'max_capacity', 'max_booking_quantity', 'event_date']);

        if ($rooms->isEmpty() || $ferries->isEmpty() || $rides->isEmpty() || $games->isEmpty()) {
            $this->command?->warn('Catalog tables look empty — run HorrorBarkWorldSeeder first.');

            return;
        }

        $hotelBookings = $this->buildHotelBookings($customerIds, $rooms);
        $ferryBookings = $this->buildHourlyBookings($customerIds, $ferries, 'ferry_id', hours: range(9, 16), perDayMin: 2, perDayMax: 5);
        $rideBookings = $this->buildHourlyBookings($customerIds, $rides, 'ride_id', hours: [9, 17], perDayMin: 2, perDayMax: 4);
        $gameBookings = $this->buildHourlyBookings($customerIds, $games, 'game_id', hours: [9, 17], perDayMin: 1, perDayMax: 3);
        $beachBookings = $this->buildBeachEventBookings($customerIds, $beachEvents);

        DB::transaction(function () use ($hotelBookings, $ferryBookings, $rideBookings, $gameBookings, $beachBookings) {
            $this->insertWithInvoices('hotel_bookings', \App\Models\HotelBooking::class, $hotelBookings);
            $this->insertWithInvoices('ferry_bookings', \App\Models\FerryBooking::class, $ferryBookings, ferryPasses: true);
            $this->insertWithInvoices('ride_bookings', \App\Models\RideBooking::class, $rideBookings);
            $this->insertWithInvoices('game_bookings', \App\Models\GameBooking::class, $gameBookings);
            $this->insertWithInvoices('beach_event_bookings', \App\Models\BeachEventBooking::class, $beachBookings);
        });

        $total = count($hotelBookings) + count($ferryBookings) + count($rideBookings) + count($gameBookings) + count($beachBookings);
        $this->command?->info(sprintf(
            'Seeded %d demo customers and %d bookings (hotels:%d ferries:%d rides:%d games:%d beach:%d).',
            count($customerIds),
            $total,
            count($hotelBookings),
            count($ferryBookings),
            count($rideBookings),
            count($gameBookings),
            count($beachBookings),
        ));
    }

    private function seedCustomers(): array
    {
        Role::findOrCreate('user', 'web');

        $firstNames = ['Lila', 'Wesley', 'Petra', 'Cassius', 'Imogen', 'Rowan', 'Theodora', 'Hollis', 'Marlowe', 'Sable', 'Octavia', 'Dorian', 'Selene', 'Caspian', 'Ines', 'Beatrix', 'Elias', 'Cordelia', 'August', 'Vesper', 'Lazarus', 'Thalia', 'Ambrose', 'Ophira'];
        $lastNames = ['Greaves', 'Carmody', 'Halloran', 'Holt', 'Wraithe', 'Mossfeld', 'Ashbourne', 'Devereux', 'Vaughn', 'Marrow', 'Crane', 'Belmont', 'Drosselmeyer', 'Whitlock', 'Sterling', 'Ravensdale', 'Pyrewood', 'Ashfall', 'Blackthorn', 'Hawthorne', 'Mortlake', 'Sallow', 'Ellsworth', 'Vance'];

        $ids = [];
        for ($i = 0; $i < self::CUSTOMER_COUNT; $i++) {
            $first = $firstNames[$i % count($firstNames)];
            $last = $lastNames[$i % count($lastNames)];
            $email = sprintf('demo+%s.%s@horrorbark.test', strtolower($first), strtolower($last));
            $createdAt = CarbonImmutable::now()->subDays(mt_rand(1, 60));

            DB::table('users')->updateOrInsert(
                ['email' => $email],
                [
                    'name' => "{$first} {$last}",
                    'email_verified_at' => $createdAt,
                    'password' => Hash::make('demo'),
                    'remember_token' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ],
            );

            $id = (int) DB::table('users')->where('email', $email)->value('id');
            DB::table('users')->where('id', $id)->update(['created_at' => $createdAt, 'updated_at' => $createdAt]);
            $ids[] = $id;
        }

        // Assign 'user' role to each demo customer.
        \App\Models\User::query()->whereIn('id', $ids)->each(fn ($u) => $u->syncRoles(['user']));

        return $ids;
    }

    private function wipeCustomerBookings(array $customerIds): void
    {
        // Invoices first (FK-free morph). Pulls down PDFs we'd otherwise orphan, but the
        // seeder doesn't generate PDFs — receipts page lazily regenerates on download.
        DB::table('invoices')->whereIn('user_id', $customerIds)->delete();

        foreach (['hotel_bookings', 'ferry_bookings', 'ride_bookings', 'game_bookings', 'beach_event_bookings'] as $table) {
            DB::table($table)->whereIn('user_id', $customerIds)->delete();
        }
    }

    private function buildHotelBookings(array $customerIds, $rooms): array
    {
        $bookings = [];
        $now = CarbonImmutable::now();

        for ($d = -self::PAST_WINDOW_DAYS; $d < self::FUTURE_WINDOW_DAYS; $d++) {
            $checkInsToday = mt_rand(1, 3);
            for ($n = 0; $n < $checkInsToday; $n++) {
                $room = $rooms->random();
                $userId = $customerIds[array_rand($customerIds)];
                $start = $now->addDays($d)->startOfDay();
                $nights = mt_rand(2, 6);
                $end = $start->addDays($nights);
                $quantity = mt_rand(1, max(1, (int) $room->max_occupancy));
                $price = (float) $room->price * $quantity * $nights;
                $status = $this->pickStatus($d < 0);

                $bookings[] = [
                    'user_id' => $userId,
                    'room_id' => $room->id,
                    'start_date' => $start,
                    'end_date' => $end,
                    'total_price' => $price,
                    'status' => $status,
                    'quantity' => $quantity,
                    'created_at' => $start->subDays(mt_rand(3, 14)),
                    'updated_at' => $start->subDays(mt_rand(0, 2)),
                ];
            }
        }

        return $bookings;
    }

    private function buildHourlyBookings(array $customerIds, $catalog, string $foreignKey, array $hours, int $perDayMin, int $perDayMax): array
    {
        $bookings = [];
        $now = CarbonImmutable::now();

        for ($d = -self::PAST_WINDOW_DAYS; $d < self::FUTURE_WINDOW_DAYS; $d++) {
            $count = mt_rand($perDayMin, $perDayMax);
            for ($n = 0; $n < $count; $n++) {
                $item = $catalog->random();
                $hour = $hours[array_rand($hours)];
                $bookingTime = $now->addDays($d)->startOfDay()->setTime($hour, 0);
                $maxQty = (int) ($item->max_booking_quantity ?? 4);
                $quantity = mt_rand(1, max(1, $maxQty));
                $price = (float) $item->price * $quantity;
                $status = $this->pickStatus($d < 0);

                $bookings[] = [
                    'user_id' => $customerIds[array_rand($customerIds)],
                    $foreignKey => $item->id,
                    'booking_time' => $bookingTime,
                    'quantity' => $quantity,
                    'total_price' => $price,
                    'status' => $status,
                    'created_at' => $bookingTime->subDays(mt_rand(2, 10)),
                    'updated_at' => $bookingTime->subDays(mt_rand(0, 1)),
                ];
            }
        }

        return $bookings;
    }

    private function buildBeachEventBookings(array $customerIds, $events): array
    {
        if ($events->isEmpty()) {
            return [];
        }

        $bookings = [];
        foreach ($events as $event) {
            $isPast = Carbon::parse($event->event_date)->lt(Carbon::today());
            $count = mt_rand(6, 14);
            for ($n = 0; $n < $count; $n++) {
                $maxQty = (int) ($event->max_booking_quantity ?? 4);
                $quantity = mt_rand(1, max(1, $maxQty));
                $price = (float) $event->price * $quantity;
                $bookingTime = Carbon::parse($event->event_date)->setTime(19, 0);

                $bookings[] = [
                    'user_id' => $customerIds[array_rand($customerIds)],
                    'beach_event_id' => $event->id,
                    'booking_date' => Carbon::parse($event->event_date)->toDateString(),
                    'quantity' => $quantity,
                    'booking_time' => $bookingTime,
                    'total_price' => $price,
                    'status' => $this->pickStatus($isPast),
                    'created_at' => $bookingTime->copy()->subDays(mt_rand(3, 14)),
                    'updated_at' => $bookingTime->copy()->subDays(mt_rand(0, 2)),
                ];
            }
        }

        return $bookings;
    }

    /** Insert in chunks; for non-canceled rows, also create matching invoice (and ferry pass for ferries). */
    private function insertWithInvoices(string $table, string $modelClass, array $rows, bool $ferryPasses = false): void
    {
        if ($rows === []) {
            return;
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table($table)->insert($chunk);
        }

        // Re-fetch ids in insertion order to attach invoices/passes.
        $userIds = array_unique(array_column($rows, 'user_id'));
        $bookings = $modelClass::query()->whereIn('user_id', $userIds)->orderBy('id')->get(['id', 'user_id', 'total_price', 'status', 'created_at']);

        $invoiceRows = [];
        $passUpdates = [];

        foreach ($bookings as $booking) {
            if ($booking->status === 'canceled') {
                continue;
            }

            $invoiceRows[] = [
                'invoice_number' => 'INV-'.$booking->created_at->format('Ymd').'-'.Str::upper(Str::random(6)),
                'invoiceable_type' => $modelClass,
                'invoiceable_id' => $booking->id,
                'user_id' => $booking->user_id,
                'amount' => $booking->total_price,
                'status' => 'issued',
                'issued_at' => $booking->created_at,
                'pdf_path' => null,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->created_at,
            ];

            if ($ferryPasses) {
                $passUpdates[$booking->id] = 'PASS-'.$booking->created_at->format('Ymd').'-'.Str::upper(Str::random(6));
            }
        }

        foreach (array_chunk($invoiceRows, 200) as $chunk) {
            DB::table('invoices')->insert($chunk);
        }

        foreach ($passUpdates as $id => $passNumber) {
            DB::table('ferry_bookings')->where('id', $id)->update(['pass_number' => $passNumber]);
        }
    }

    private function pickStatus(bool $isPast): string
    {
        $mix = $isPast ? self::PAST_STATUS_MIX : self::FUTURE_STATUS_MIX;
        $roll = mt_rand(0, 999) / 1000;
        $cumulative = 0.0;
        foreach ($mix as $status => $weight) {
            $cumulative += $weight;
            if ($roll < $cumulative) {
                return $status;
            }
        }

        return array_key_first($mix);
    }
}
