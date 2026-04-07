<?php

namespace App\Http\Controllers;

use App\Models\BeachEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BeachEventController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', Rule::in(['date_asc', 'date_desc', 'price_asc', 'price_desc', 'name_asc', 'name_desc'])],
        ]);

        $query = BeachEvent::query()->with('owner', 'island');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                        $ownerQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('event_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('event_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
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

        return view('pages.beach-events.index', compact('beachEvents', 'filters'));
    }
}
