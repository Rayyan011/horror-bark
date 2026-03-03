<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Game;
use App\Models\GameBooking;
use App\Models\Ride;
use App\Models\RideBooking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotAvailabilityController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'in:ferry,ride,game'],
            'id' => ['required', 'integer'],
            'date' => ['required', 'date'],
        ]);

        $date = Carbon::parse($request->date);
        $type = $request->type;
        $id = $request->id;

        $slots = match ($type) {
            'ferry' => $this->ferrySlots($id, $date),
            'ride' => $this->rideSlots($id, $date),
            'game' => $this->gameSlots($id, $date),
        };

        return response()->json(['slots' => $slots]);
    }

    private function ferrySlots(int $id, Carbon $date): array
    {
        $ferry = Ferry::findOrFail($id);
        $slots = [];

        for ($hour = 9; $hour <= 16; $hour++) {
            $time = $date->copy()->setTime($hour, 0, 0);
            $booked = FerryBooking::where('ferry_id', $id)
                ->where('booking_time', $time)
                ->where('status', '!=', 'canceled')
                ->sum('quantity');

            $remaining = $ferry->max_capacity - $booked;

            $slots[] = [
                'time' => $time->format('H:i'),
                'datetime' => $time->toDateTimeString(),
                'remaining' => max(0, $remaining),
                'total' => $ferry->max_capacity,
                'available' => $remaining > 0,
            ];
        }

        return $slots;
    }

    private function rideSlots(int $id, Carbon $date): array
    {
        $ride = Ride::findOrFail($id);
        $slots = [];

        foreach ([9, 17] as $hour) {
            $time = $date->copy()->setTime($hour, 0, 0);
            $booked = RideBooking::where('ride_id', $id)
                ->where('booking_time', $time)
                ->where('status', '!=', 'canceled')
                ->sum('quantity');

            $remaining = $ride->max_capacity - $booked;

            $slots[] = [
                'time' => $time->format('H:i'),
                'datetime' => $time->toDateTimeString(),
                'remaining' => max(0, $remaining),
                'total' => $ride->max_capacity,
                'available' => $remaining > 0,
            ];
        }

        return $slots;
    }

    private function gameSlots(int $id, Carbon $date): array
    {
        $game = Game::findOrFail($id);
        $slots = [];

        foreach ([9, 17] as $hour) {
            $time = $date->copy()->setTime($hour, 0, 0);
            $booked = GameBooking::where('game_id', $id)
                ->where('booking_time', $time)
                ->where('status', '!=', 'canceled')
                ->sum('quantity');

            $remaining = $game->max_capacity - $booked;

            $slots[] = [
                'time' => $time->format('H:i'),
                'datetime' => $time->toDateTimeString(),
                'remaining' => max(0, $remaining),
                'total' => $game->max_capacity,
                'available' => $remaining > 0,
            ];
        }

        return $slots;
    }
}
