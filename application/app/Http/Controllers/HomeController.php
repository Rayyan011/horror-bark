<?php

namespace App\Http\Controllers;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\Hotel;
use App\Models\Island;
use App\Models\Promotion;
use App\Models\Ride;
use App\Services\HorrorAtlasService;
use App\Support\HorrorGeneratedMediaCatalog;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index(HorrorAtlasService $atlas)
    {
        $hotels = Hotel::query()
            ->with('rooms')
            ->orderBy('name')
            ->get();

        $islands = Island::query()
            ->orderBy('name')
            ->get();

        $rides = Ride::query()
            ->with('island')
            ->orderBy('name')
            ->get();

        $games = Game::query()
            ->with('island')
            ->orderBy('name')
            ->get();

        $beachEvents = BeachEvent::query()
            ->with('island', 'owner')
            ->orderBy('event_date')
            ->get();

        $ferries = Ferry::query()
            ->with('island')
            ->orderBy('name')
            ->get();

        $promotions = Promotion::query()
            ->active()
            ->orderByDesc('starts_at')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $hauntRides = Ride::query()
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        $hauntGames = Game::query()
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        $hauntBeachEvents = BeachEvent::query()
            ->orderBy('event_date')
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        $otherHaunts = $this->buildOtherHaunts($hauntRides, $hauntGames, $hauntBeachEvents);
        $atlasData = $atlas->build($islands, $hotels, $rides, $games, $beachEvents, $ferries);

        return view('pages.home', compact(
            'hotels',
            'islands',
            'rides',
            'games',
            'beachEvents',
            'ferries',
            'promotions',
            'otherHaunts',
            'atlasData',
        ));
    }

    private function buildOtherHaunts(Collection $rides, Collection $games, Collection $beachEvents): Collection
    {
        $queues = [
            'ride' => $rides->values(),
            'game' => $games->values(),
            'beach_event' => $beachEvents->values(),
        ];

        $positions = [
            'ride' => 0,
            'game' => 0,
            'beach_event' => 0,
        ];

        $otherHaunts = collect();

        while ($otherHaunts->count() < 8) {
            $addedInRound = false;

            foreach (array_keys($queues) as $type) {
                $item = $queues[$type]->get($positions[$type]);

                if (! $item) {
                    continue;
                }

                $otherHaunts->push($this->normalizeHauntItem($type, $item));
                $positions[$type]++;
                $addedInRound = true;

                if ($otherHaunts->count() === 8) {
                    break;
                }
            }

            if (! $addedInRound) {
                break;
            }
        }

        return $otherHaunts;
    }

    private function normalizeHauntItem(string $type, Ride|Game|BeachEvent $item): array
    {
        $defaultDescription = match ($type) {
            'ride' => 'Rides forged from twisted iron and fog. The laughter may not be your own.',
            'game' => 'Test your courage through midnight trials and sinister games of chance.',
            default => 'Midnight gatherings where black ocean meets gray sand and distant music.',
        };

        $href = match ($type) {
            'ride' => route('themepark.index', ['section' => 'rides', 'search' => $item->name]),
            'game' => route('themepark.index', ['section' => 'games', 'search' => $item->name]),
            default => route('beach-events.index', ['search' => $item->name]),
        };

        $linkText = match ($type) {
            'ride' => 'Acquire Ticket',
            'game' => 'Enter Game',
            default => 'Observe Events',
        };

        $images = is_array($item->images) ? $item->images : [];

        return [
            'type' => $type,
            'title' => $item->name,
            'description' => filled($item->description ?? null) ? $item->description : $defaultDescription,
            'images' => $images,
            'image' => empty($images) ? HorrorGeneratedMediaCatalog::path('fallbacks', $type === 'beach_event' ? 'beach-event' : $type) : null,
            'href' => $href,
            'linkText' => $linkText,
        ];
    }
}
