<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelBooking;
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

        $query = Hotel::query()->with('rooms');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        if (
            !empty($filters['min_price'])
            || !empty($filters['max_price'])
            || !empty($filters['min_occupancy'])
        ) {
            $query->whereHas('rooms', function ($builder) use ($filters) {
                if (!empty($filters['min_price'])) {
                    $builder->where('price', '>=', $filters['min_price']);
                }

                if (!empty($filters['max_price'])) {
                    $builder->where('price', '<=', $filters['max_price']);
                }

                if (!empty($filters['min_occupancy'])) {
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

        return view('pages.hotels.index', compact('hotels', 'filters'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['rooms' => fn ($query) => $query->where('status', 'available')]);

        $today = Carbon::today();

        foreach ($hotel->rooms as $room) {
            $bookedQuantity = HotelBooking::where('room_id', $room->id)
                ->where('status', '!=', 'canceled')
                ->where('end_date', '>', $today)
                ->sum('quantity');

            $room->available_spots = max(0, $room->max_occupancy - $bookedQuantity);
        }

        return view('pages.hotels.show', compact('hotel'));
    }
}
