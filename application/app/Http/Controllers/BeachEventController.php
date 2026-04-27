<?php

namespace App\Http\Controllers;

use App\Models\BeachEvent;
use App\Services\IslandAccessService;
use App\Support\CatalogFilterBounds;
use App\Support\IslandTypeCatalogFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class BeachEventController extends Controller
{
    public function index(Request $request, IslandAccessService $islandAccessService)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'island_type' => ['nullable', IslandTypeCatalogFilter::rule()],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_capacity' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', Rule::in(['date_asc', 'date_desc', 'price_asc', 'price_desc', 'name_asc', 'name_desc'])],
        ]);

        $priceBounds = CatalogFilterBounds::price(
            BeachEvent::query()->min('price'),
            BeachEvent::query()->max('price'),
        );
        $capacityBounds = CatalogFilterBounds::quantity(
            BeachEvent::query()->min('max_capacity'),
            BeachEvent::query()->max('max_capacity'),
        );
        $priceRange = CatalogFilterBounds::normalizeRange(
            $priceBounds,
            $filters['min_price'] ?? null,
            $filters['max_price'] ?? null,
        );

        $filters['min_price'] = $priceRange['min'];
        $filters['max_price'] = $priceRange['max'];
        $filters['min_capacity'] = CatalogFilterBounds::normalizeSingle(
            $capacityBounds,
            $filters['min_capacity'] ?? null,
        );

        $query = BeachEvent::query()->with('owner', 'island');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                        $ownerQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        IslandTypeCatalogFilter::apply(
            $query,
            $filters['island_type'] ?? null,
            IslandAccessService::PICNIC_ISLAND,
        );

        if (! empty($filters['date_from'])) {
            $query->whereDate('event_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('event_date', '<=', $filters['date_to']);
        }

        if ($filters['min_price'] > $priceBounds['min']) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if ($filters['max_price'] < $priceBounds['max']) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if ($filters['min_capacity'] > $capacityBounds['min']) {
            $query->where('max_capacity', '>=', $filters['min_capacity']);
        }

        $sort = $filters['sort'] ?? 'date_asc';
        match ($sort) {
            'date_desc' => $query->orderBy('event_date', 'desc'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('event_date', 'asc'),
        };

        $beachEvents = $query->paginate(12)->withQueryString();

        $filterBounds = [
            'price' => $priceBounds,
            'capacity' => $capacityBounds,
        ];
        $islandTypeOptions = IslandTypeCatalogFilter::options();
        $eventTimeOptions = collect(range(0, 23))
            ->map(fn (int $hour): string => sprintf('%02d:00', $hour))
            ->all();
        $hotelStayWindows = $request->user()
            ? $islandAccessService->confirmedHotelStayWindowsForActivities($request->user())
            : [];
        $hotelStayDateOptions = $islandAccessService->dateOptionsWithFutureSlots(
            $islandAccessService->dateOptionsFromStayWindows($hotelStayWindows),
            $eventTimeOptions,
        );
        $beachEventBookingConfigs = $beachEvents->getCollection()
            ->mapWithKeys(function (BeachEvent $event) use ($eventTimeOptions, $hotelStayWindows, $hotelStayDateOptions): array {
                $eventDate = Carbon::parse($event->event_date)->toDateString();
                $dateOptions = collect($hotelStayDateOptions)
                    ->where('value', $eventDate)
                    ->values()
                    ->all();
                $hasEligibleStay = count($dateOptions) > 0;

                return [
                    $event->id => [
                        'mode' => 'hotel-gated-event',
                        'hotelStayWindows' => $hotelStayWindows,
                        'dateOptions' => $dateOptions,
                        'dateMin' => $eventDate,
                        'dateMax' => $eventDate,
                        'timeOptions' => $eventTimeOptions,
                        'disabled' => ! $hasEligibleStay,
                        'disabledReason' => 'Book a confirmed hotel stay covering this event date before booking.',
                        'invalidDateMessage' => 'Choose the event date during your confirmed hotel stay.',
                        'futureMessage' => 'Choose a future event time.',
                        'rulesHint' => 'Choose a time on the event date during your confirmed hotel stay.',
                        'submitLabel' => 'Book event',
                    ],
                ];
            })
            ->all();

        return view('pages.beach-events.index', compact('beachEvents', 'filters', 'filterBounds', 'islandTypeOptions', 'beachEventBookingConfigs'));
    }
}
