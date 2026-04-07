<?php

namespace App\Http\Controllers;

use App\Models\Ferry;
use App\Models\Island;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FerryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'island_id' => ['nullable', 'integer', 'exists:islands,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_capacity' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', Rule::in(['name_asc', 'name_desc', 'price_asc', 'price_desc'])],
        ]);

        $query = Ferry::query()->with('island');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['island_id'])) {
            $query->where('island_id', $filters['island_id']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['min_capacity'])) {
            $query->where('max_capacity', '>=', $filters['min_capacity']);
        }

        $sort = $filters['sort'] ?? 'name_asc';
        if (in_array($sort, ['price_asc', 'price_desc'], true)) {
            $query->orderBy('price', $sort === 'price_asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('name', $sort === 'name_desc' ? 'desc' : 'asc');
        }

        $ferries = $query->paginate(12)->withQueryString();
        $islands = Island::query()->orderBy('name')->get(['id', 'name', 'type']);

        return view('pages.ferries.index', compact('ferries', 'filters', 'islands'));
    }
}
