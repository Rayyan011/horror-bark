<?php

namespace App\Services;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\HotelBooking;
use App\Models\Ride;
use App\Models\User;
use Carbon\Carbon;

class IslandAccessService
{
    public const HORROR_ISLAND = 'Horror-Island';

    public const PICNIC_ISLAND = 'Picnic-Island';

    public const REQUIRED_STAY_ERROR = 'A confirmed hotel stay is required before booking this activity.';

    public function ferryRequiresHotel(Ferry $ferry): bool
    {
        return $this->isHorrorIsland($ferry->island?->type ?? self::HORROR_ISLAND);
    }

    public function rideRequiresHotel(Ride $ride): bool
    {
        return true;
    }

    public function gameRequiresHotel(Game $game): bool
    {
        return true;
    }

    public function beachEventRequiresHotel(BeachEvent $beachEvent): bool
    {
        return true;
    }

    public function hasConfirmedHotelStayAt(User $user, Carbon $activityAt): bool
    {
        return HotelBooking::query()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('start_date', '<=', $activityAt)
            ->where('end_date', '>', $activityAt)
            ->exists();
    }

    public function hasConfirmedHotelStayForFerryAt(User $user, Carbon $ferryAt): bool
    {
        return HotelBooking::query()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('start_date', '<=', $ferryAt)
            ->whereDate('end_date', '>=', $ferryAt->toDateString())
            ->exists();
    }

    public function hotelStayAccessError(User $user, string $noStayMessage, string $outsideStayPrefix, bool $includeCheckoutDay = false): string
    {
        $windows = $includeCheckoutDay
            ? $this->confirmedHotelStayWindowsForFerry($user)
            : $this->confirmedHotelStayWindowsForActivities($user);

        if (count($windows) === 0) {
            return $noStayMessage;
        }

        return $outsideStayPrefix.': '.collect($windows)->pluck('label')->implode('; ').'.';
    }

    /**
     * @return array<int, array{start: string, end: string, label: string}>
     */
    public function confirmedHotelStayWindowsForActivities(User $user): array
    {
        $today = Carbon::today();

        return HotelBooking::query()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereDate('end_date', '>', $today->toDateString())
            ->orderBy('start_date')
            ->get(['start_date', 'end_date'])
            ->map(function (HotelBooking $booking) use ($today): ?array {
                $start = Carbon::parse($booking->start_date)->startOfDay();
                $end = Carbon::parse($booking->end_date)->startOfDay()->subDay();

                if ($start->lt($today)) {
                    $start = $today->copy();
                }

                if ($end->lt($start)) {
                    return null;
                }

                return [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                    'label' => $start->format('M j, Y').' - '.$end->format('M j, Y'),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{start: string, end: string, label: string}>
     */
    public function confirmedHotelStayWindowsForFerry(User $user): array
    {
        $today = Carbon::today();

        return HotelBooking::query()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereDate('end_date', '>=', $today->toDateString())
            ->orderBy('start_date')
            ->get(['start_date', 'end_date'])
            ->map(function (HotelBooking $booking) use ($today): ?array {
                $start = Carbon::parse($booking->start_date)->startOfDay();
                $end = Carbon::parse($booking->end_date)->startOfDay();

                if ($start->lt($today)) {
                    $start = $today->copy();
                }

                if ($end->lt($start)) {
                    return null;
                }

                return [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                    'label' => $start->format('M j, Y').' - '.$end->format('M j, Y'),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{start: string, end: string, label: string}>  $windows
     * @return array<int, array{value: string, label: string}>
     */
    public function dateOptionsFromStayWindows(array $windows): array
    {
        return collect($windows)
            ->flatMap(function (array $window): array {
                $start = Carbon::parse($window['start'])->startOfDay();
                $end = Carbon::parse($window['end'])->startOfDay();
                $dates = [];

                while ($start->lte($end)) {
                    $dates[] = [
                        'value' => $start->toDateString(),
                        'label' => $start->format('M j, Y'),
                    ];

                    $start->addDay();
                }

                return $dates;
            })
            ->unique('value')
            ->sortBy('value')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{value: string, label: string}>  $dateOptions
     * @param  array<int, string>  $timeOptions
     * @return array<int, array{value: string, label: string}>
     */
    public function dateOptionsWithFutureSlots(array $dateOptions, array $timeOptions): array
    {
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i');

        return collect($dateOptions)
            ->filter(function (array $option) use ($today, $nowTime, $timeOptions): bool {
                if ($option['value'] > $today) {
                    return true;
                }

                if ($option['value'] < $today) {
                    return false;
                }

                return collect($timeOptions)->contains(fn (string $time): bool => $time > $nowTime);
            })
            ->values()
            ->all();
    }

    private function isHorrorIsland(?string $type): bool
    {
        return $type === self::HORROR_ISLAND;
    }
}
