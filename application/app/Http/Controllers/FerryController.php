<?php

namespace App\Http\Controllers;

use App\Models\Ferry;
use App\Models\Island;
use App\Support\CatalogFilterBounds;
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

        $priceBounds = CatalogFilterBounds::price(
            Ferry::query()->min('price'),
            Ferry::query()->max('price'),
        );
        $capacityBounds = CatalogFilterBounds::quantity(
            Ferry::query()->min('max_capacity'),
            Ferry::query()->max('max_capacity'),
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

        $query = Ferry::query()->with('island');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if (! empty($filters['island_id'])) {
            $query->where('island_id', $filters['island_id']);
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

        $sort = $filters['sort'] ?? 'name_asc';
        if (in_array($sort, ['price_asc', 'price_desc'], true)) {
            $query->orderBy('price', $sort === 'price_asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('name', $sort === 'name_desc' ? 'desc' : 'asc');
        }

        $ferries = $query->paginate(12)->withQueryString();
        $islands = Island::query()->orderBy('name')->get(['id', 'name', 'type']);

        $filterBounds = [
            'price' => $priceBounds,
            'capacity' => $capacityBounds,
        ];

        return view('pages.ferries.index', compact('ferries', 'filters', 'islands', 'filterBounds'));
    }
}
