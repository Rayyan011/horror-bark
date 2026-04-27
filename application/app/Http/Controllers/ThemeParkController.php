<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Ride;
use App\Services\IslandAccessService;
use App\Support\CatalogFilterBounds;
use App\Support\IslandTypeCatalogFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ThemeParkController extends Controller
{
    public function index(Request $request, IslandAccessService $islandAccessService): View
    {
        $filters = $request->validate([
            'section' => ['nullable', Rule::in(['all', 'rides', 'games'])],
            'search' => ['nullable', 'string', 'max:120'],
            'island_type' => ['nullable', IslandTypeCatalogFilter::rule()],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_capacity' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', Rule::in(['name_asc', 'name_desc', 'price_asc', 'price_desc'])],
        ]);

        $priceValues = collect([
            Ride::query()->min('price'),
            Game::query()->min('price'),
        ])->filter(fn ($value) => ! is_null($value));
        $maxPriceValues = collect([
            Ride::query()->max('price'),
            Game::query()->max('price'),
        ])->filter(fn ($value) => ! is_null($value));
        $capacityValues = collect([
            Ride::query()->min('max_capacity'),
            Game::query()->min('max_capacity'),
        ])->filter(fn ($value) => ! is_null($value));
        $maxCapacityValues = collect([
            Ride::query()->max('max_capacity'),
            Game::query()->max('max_capacity'),
        ])->filter(fn ($value) => ! is_null($value));

        $priceBounds = CatalogFilterBounds::price(
            $priceValues->isEmpty() ? null : $priceValues->min(),
            $maxPriceValues->isEmpty() ? null : $maxPriceValues->max(),
        );
        $capacityBounds = CatalogFilterBounds::quantity(
            $capacityValues->isEmpty() ? null : $capacityValues->min(),
            $maxCapacityValues->isEmpty() ? null : $maxCapacityValues->max(),
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

        $section = $filters['section'] ?? 'all';

        $ridesQuery = Ride::query()->with('island');
        $gamesQuery = Game::query()->with('island');

        $sort = $filters['sort'] ?? 'name_asc';
        $this->applyActivityFilters($ridesQuery, $filters, $priceBounds, $capacityBounds);
        $this->applyActivityFilters($gamesQuery, $filters, $priceBounds, $capacityBounds);
        IslandTypeCatalogFilter::apply($ridesQuery, $filters['island_type'] ?? null, IslandAccessService::HORROR_ISLAND);
        IslandTypeCatalogFilter::apply($gamesQuery, $filters['island_type'] ?? null, IslandAccessService::HORROR_ISLAND);

        $activities = collect();

        if ($section !== 'games') {
            $activities = $activities->concat(
                $ridesQuery->get()->map(function (Ride $ride) {
                    $ride->catalog_type = 'ride';

                    return $ride;
                })
            );
        }

        if ($section !== 'rides') {
            $activities = $activities->concat(
                $gamesQuery->get()->map(function (Game $game) {
                    $game->catalog_type = 'game';

                    return $game;
                })
            );
        }

        $activities = $this->paginateActivities(
            $this->sortActivities($activities, $sort),
            12,
        );

        $filterBounds = [
            'price' => $priceBounds,
            'capacity' => $capacityBounds,
        ];
        $islandTypeOptions = IslandTypeCatalogFilter::options();
        $slotTimeOptions = ['09:00', '17:00'];
        $hotelStayWindows = $request->user()
            ? $islandAccessService->confirmedHotelStayWindowsForActivities($request->user())
            : [];
        $hotelStayDateOptions = $islandAccessService->dateOptionsWithFutureSlots(
            $islandAccessService->dateOptionsFromStayWindows($hotelStayWindows),
            $slotTimeOptions,
        );
        $hasEligibleStay = count($hotelStayDateOptions) > 0;
        $dateMax = $hasEligibleStay ? collect($hotelStayDateOptions)->max('value') : null;
        $activityBookingConfigs = $activities->getCollection()
            ->mapWithKeys(fn ($activity): array => [
                $activity->catalog_type.'_'.$activity->id => [
                    'mode' => 'hotel-gated-slot',
                    'hotelStayWindows' => $hotelStayWindows,
                    'dateOptions' => $hotelStayDateOptions,
                    'dateMin' => now()->toDateString(),
                    'dateMax' => $dateMax,
                    'timeOptions' => $slotTimeOptions,
                    'disabled' => ! $hasEligibleStay,
                    'disabledReason' => 'Book a confirmed hotel stay before booking rides or games.',
                    'invalidDateMessage' => 'Choose a date during your confirmed hotel stay.',
                    'futureMessage' => 'Choose a future '.($activity->catalog_type === 'game' ? 'game' : 'ride').' time.',
                    'rulesHint' => 'Choose a date during your confirmed hotel stay. Sessions are available at 09:00 or 17:00.',
                    'submitLabel' => $activity->catalog_type === 'game' ? 'Book game' : 'Book ride',
                ],
            ])
            ->all();

        return view('pages.themepark.index', compact('activities', 'filters', 'filterBounds', 'islandTypeOptions', 'activityBookingConfigs'));
    }

    private function applyActivityFilters($query, array $filters, array $priceBounds, array $capacityBounds): void
    {
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
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
    }

    private function sortActivities(Collection $activities, string $sort): Collection
    {
        return $activities->sort(function ($left, $right) use ($sort) {
            $primary = match ($sort) {
                'price_asc' => $left->price <=> $right->price,
                'price_desc' => $right->price <=> $left->price,
                'name_desc' => strcasecmp($right->name, $left->name),
                default => strcasecmp($left->name, $right->name),
            };

            if ($primary !== 0) {
                return $primary;
            }

            return strcasecmp($left->name, $right->name);
        })->values();
    }

    private function paginateActivities(Collection $activities, int $perPage): LengthAwarePaginator
    {
        $currentPage = Paginator::resolveCurrentPage();

        $paginator = new Paginator(
            $activities->forPage($currentPage, $perPage)->values(),
            $activities->count(),
            $perPage,
            $currentPage,
            [
                'pageName' => 'page',
                'path' => url()->current(),
            ],
        );

        $paginator->appends(request()->query());

        return $paginator;
    }
}
