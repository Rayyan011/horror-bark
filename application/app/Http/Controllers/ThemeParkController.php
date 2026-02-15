<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Ride;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ThemeParkController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'section' => ['nullable', Rule::in(['all', 'rides', 'games'])],
            'search' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_capacity' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', Rule::in(['name_asc', 'name_desc', 'price_asc', 'price_desc'])],
        ]);

        $section = $filters['section'] ?? 'all';

        $ridesQuery = Ride::query()->with('island');
        $gamesQuery = Game::query()->with('island');

        $this->applyActivityFilters($ridesQuery, $filters);
        $this->applyActivityFilters($gamesQuery, $filters);

        $sort = $filters['sort'] ?? 'name_asc';
        $this->applyActivitySort($ridesQuery, $sort);
        $this->applyActivitySort($gamesQuery, $sort);

        $rides = $section === 'games'
            ? $this->emptyPaginator('ride_page')
            : $ridesQuery->paginate(9, ['*'], 'ride_page')->withQueryString();

        $games = $section === 'rides'
            ? $this->emptyPaginator('game_page')
            : $gamesQuery->paginate(9, ['*'], 'game_page')->withQueryString();

        return view('pages.themepark.index', compact('rides', 'games', 'filters'));
    }

    private function applyActivityFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where('name', 'like', '%' . $search . '%');
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
    }

    private function applyActivitySort($query, string $sort): void
    {
        if (in_array($sort, ['price_asc', 'price_desc'], true)) {
            $query->orderBy('price', $sort === 'price_asc' ? 'asc' : 'desc');
            return;
        }

        $query->orderBy('name', $sort === 'name_desc' ? 'desc' : 'asc');
    }

    private function emptyPaginator(string $pageName): LengthAwarePaginator
    {
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 9, 1, [
            'pageName' => $pageName,
            'path' => url()->current(),
        ]);
        $paginator->appends(request()->query());

        return $paginator;
    }
}
