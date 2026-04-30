<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Room;
use App\Support\CatalogFilterBounds;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_occupancy' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', Rule::in(['name_asc', 'name_desc', 'price_asc', 'price_desc'])],
        ]);

        $priceBounds = CatalogFilterBounds::price(
            Room::query()->min('price'),
            Room::query()->max('price'),
        );
        $occupancyBounds = CatalogFilterBounds::quantity(
            Room::query()->min('max_occupancy'),
            Room::query()->max('max_occupancy'),
        );
        $priceRange = CatalogFilterBounds::normalizeRange(
            $priceBounds,
            $filters['min_price'] ?? null,
            $filters['max_price'] ?? null,
        );

        $filters['min_price'] = $priceRange['min'];
        $filters['max_price'] = $priceRange['max'];
        $filters['min_occupancy'] = CatalogFilterBounds::normalizeSingle(
            $occupancyBounds,
            $filters['min_occupancy'] ?? null,
        );

        $query = Hotel::query()->with('rooms');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if (
            ! CatalogFilterBounds::isDefaultRange($priceBounds, $filters['min_price'], $filters['max_price'])
            || $filters['min_occupancy'] > $occupancyBounds['min']
        ) {
            $query->whereHas('rooms', function ($builder) use ($filters, $priceBounds, $occupancyBounds) {
                if ($filters['min_price'] > $priceBounds['min']) {
                    $builder->where('price', '>=', $filters['min_price']);
                }

                if ($filters['max_price'] < $priceBounds['max']) {
                    $builder->where('price', '<=', $filters['max_price']);
                }

                if ($filters['min_occupancy'] > $occupancyBounds['min']) {
                    $builder->where('max_occupancy', '>=', $filters['min_occupancy']);
                }
            });
        }

        $sort = $filters['sort'] ?? 'name_asc';
        if (in_array($sort, ['price_asc', 'price_desc'], true)) {
            $query->withMin('rooms', 'price')
                ->orderBy('rooms_min_price', $sort === 'price_asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('name', $sort === 'name_desc' ? 'desc' : 'asc');
        }

        $hotels = $query->paginate(12)->withQueryString();

        $filterBounds = [
            'price' => $priceBounds,
            'occupancy' => $occupancyBounds,
        ];

        return view('pages.hotels.index', compact('hotels', 'filters', 'filterBounds'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['rooms' => fn ($query) => $query->where('status', 'available')]);

        // Availability is shown for tonight (today → tomorrow). The booking modal lets
        // the guest pick real dates, and BookingCheckoutService re-validates against them.
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDay();

        foreach ($hotel->rooms as $room) {
            $overlappingQuantity = HotelBooking::query()
                ->where('room_id', $room->id)
                ->where('status', '!=', 'canceled')
                ->where('start_date', '<', $endDate)
                ->where('end_date', '>', $startDate)
                ->sum('quantity');

            $room->available_spots = max(0, $room->max_occupancy - $overlappingQuantity);
        }

        return view('pages.hotels.show', compact('hotel'));
    }
}
