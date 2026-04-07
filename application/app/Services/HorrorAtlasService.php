<?php

namespace App\Services;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\Hotel;
use App\Models\Island;
use App\Models\Ride;
use App\Support\HorrorGeneratedMediaCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HorrorAtlasService
{
    public function build(
        Collection $islands,
        Collection $hotels,
        Collection $rides,
        Collection $games,
        Collection $beachEvents,
        Collection $ferries,
    ): array {
        $bounds = $this->resolveBounds(
            $islands,
            $hotels,
            $rides,
            $games,
            $beachEvents,
        );

        $zones = $islands
            ->map(fn (Island $island) => $this->mapZone($island, $bounds))
            ->sortBy('name')
            ->values();

        $locations = collect()
            ->concat($hotels->map(fn (Hotel $hotel) => $this->mapHotel($hotel, $bounds)))
            ->concat($rides->map(fn (Ride $ride) => $this->mapRide($ride, $bounds)))
            ->concat($games->map(fn (Game $game) => $this->mapGame($game, $bounds)))
            ->concat($beachEvents->map(fn (BeachEvent $event) => $this->mapBeachEvent($event, $bounds)))
            ->concat($ferries->map(fn (Ferry $ferry) => $this->mapFerry($ferry, $bounds)))
            ->sortBy(fn (array $location) => [$this->categoryPriority($location['category']), $location['name']])
            ->values();

        return [
            'zones' => $zones,
            'locations' => $locations,
            'featured' => $locations->take(6)->values(),
        ];
    }

    private function mapZone(Island $island, ?array $bounds): array
    {
        ['x' => $x, 'y' => $y] = $this->positionFor($island, $bounds, 50, 50);

        return [
            'name' => $island->name,
            'type' => $island->type,
            'description' => $island->description,
            'x' => $x,
            'y' => $y,
        ];
    }

    private function mapHotel(Hotel $hotel, ?array $bounds): array
    {
        ['x' => $x, 'y' => $y] = $this->positionFor($hotel, $bounds, 34, 28);
        $district = filled($hotel->location)
            ? trim(Str::before($hotel->location, '·'))
            : 'Horror-Bark';
        $cheapestRoom = $hotel->relationLoaded('rooms')
            ? $hotel->rooms->sortBy('price')->first()
            : null;

        return [
            'slug' => $this->slug('hotel', $hotel),
            'name' => $hotel->name,
            'category' => 'hotel',
            'typeLabel' => 'Lodging & Rituals',
            'zoneLabel' => 'District',
            'zoneName' => $district,
            'description' => $hotel->description ?: 'A candlelit residence arranged for guests who prefer velvet and cold stone.',
            'x' => $x,
            'y' => $y,
            'icon' => 'bed',
            'image' => $this->imageFor($hotel->images, HorrorGeneratedMediaCatalog::path('fallbacks', 'hotel')),
            'eyebrow' => 'Hotel',
            'stat' => $cheapestRoom ? 'From MVR '.number_format($cheapestRoom->price, 2).' / night' : 'Reservations available',
            'secondary' => $hotel->location ?: $district,
            'href' => route('hotels.show', $hotel),
            'ctaLabel' => 'View Hotel',
        ];
    }

    private function mapRide(Ride $ride, ?array $bounds): array
    {
        ['x' => $x, 'y' => $y] = $this->positionFor($ride, $bounds, 42, 56);

        return [
            'slug' => $this->slug('ride', $ride),
            'name' => $ride->name,
            'category' => 'ride',
            'typeLabel' => 'Ritual Ride',
            'zoneLabel' => 'Grounds',
            'zoneName' => $ride->island->name ?? 'Shadow Park',
            'description' => $ride->description ?: 'A polished descent engineered for those who prefer terror with ceremony.',
            'x' => $x,
            'y' => $y,
            'icon' => 'rocket_launch',
            'image' => $this->imageFor($ride->images, HorrorGeneratedMediaCatalog::path('fallbacks', 'ride')),
            'eyebrow' => 'Ride',
            'stat' => 'MVR '.number_format($ride->price, 2),
            'secondary' => 'Capacity '.$ride->max_capacity,
            'href' => route('themepark.index', ['section' => 'rides', 'search' => $ride->name, 'focus' => $this->slug('ride', $ride)]),
            'ctaLabel' => 'Find Ride',
        ];
    }

    private function mapGame(Game $game, ?array $bounds): array
    {
        ['x' => $x, 'y' => $y] = $this->positionFor($game, $bounds, 55, 42);

        return [
            'slug' => $this->slug('game', $game),
            'name' => $game->name,
            'category' => 'game',
            'typeLabel' => 'Trial Ground',
            'zoneLabel' => 'Grounds',
            'zoneName' => $game->island->name ?? 'Lantern Hollow',
            'description' => $game->description ?: 'A midnight trial of nerve, chance, and ceremonial skill.',
            'x' => $x,
            'y' => $y,
            'icon' => 'casino',
            'image' => $this->imageFor($game->images, HorrorGeneratedMediaCatalog::path('fallbacks', 'game')),
            'eyebrow' => 'Game',
            'stat' => 'MVR '.number_format($game->price, 2),
            'secondary' => 'Up to '.$game->max_booking_quantity.' per booking',
            'href' => route('themepark.index', ['section' => 'games', 'search' => $game->name, 'focus' => $this->slug('game', $game)]),
            'ctaLabel' => 'Find Game',
        ];
    }

    private function mapBeachEvent(BeachEvent $event, ?array $bounds): array
    {
        ['x' => $x, 'y' => $y] = $this->positionFor($event, $bounds, 76, 66);

        return [
            'slug' => $this->slug('beach-event', $event),
            'name' => $event->name,
            'category' => 'beach_event',
            'typeLabel' => 'Moonlit Gathering',
            'zoneLabel' => 'Shore',
            'zoneName' => $event->island->name ?? 'Pale Moon Strand',
            'description' => $event->description ?: 'A shoreline vigil staged where the black tide turns silver under the moon.',
            'x' => $x,
            'y' => $y,
            'icon' => 'local_fire_department',
            'image' => $this->imageFor($event->images, HorrorGeneratedMediaCatalog::path('fallbacks', 'beach-event')),
            'eyebrow' => 'Beach Event',
            'stat' => $event->event_date ? $event->event_date->format('F d') : 'Nightfall gathering',
            'secondary' => 'MVR '.number_format($event->price, 2),
            'href' => route('beach-events.index', ['search' => $event->name]),
            'ctaLabel' => 'Observe Event',
        ];
    }

    private function mapFerry(Ferry $ferry, ?array $bounds): array
    {
        ['x' => $x, 'y' => $y] = $this->positionFor(
            $ferry,
            $bounds,
            data_get($ferry, 'island.map_x', 70),
            data_get($ferry, 'island.map_y', 68),
        );

        return [
            'slug' => $this->slug('ferry', $ferry),
            'name' => $ferry->name,
            'category' => 'ferry',
            'typeLabel' => 'Passage & Approach',
            'zoneLabel' => 'Destination',
            'zoneName' => $ferry->island->name ?? 'Coven Quay',
            'description' => $ferry->description ?: 'A moonlit crossing that delivers guests through black water and harbor bells.',
            'x' => $x,
            'y' => $y,
            'icon' => 'directions_boat',
            'image' => $this->imageFor(data_get($ferry, 'island.images'), HorrorGeneratedMediaCatalog::path('fallbacks', 'ferry')),
            'eyebrow' => 'Ferry',
            'stat' => 'MVR '.number_format($ferry->price, 2),
            'secondary' => 'Capacity '.$ferry->max_capacity,
            'href' => route('ferries.index', ['search' => $ferry->name]),
            'ctaLabel' => 'Book Passage',
        ];
    }

    private function resolveBounds(Collection ...$collections): ?array
    {
        $points = collect($collections)
            ->flatMap(fn (Collection $collection) => $collection)
            ->filter(fn (Model $model) => filled($model->latitude) && filled($model->longitude));

        if ($points->isEmpty()) {
            return null;
        }

        return [
            'min_lat' => (float) $points->min('latitude'),
            'max_lat' => (float) $points->max('latitude'),
            'min_lng' => (float) $points->min('longitude'),
            'max_lng' => (float) $points->max('longitude'),
        ];
    }

    private function positionFor(Model $record, ?array $bounds, float $defaultX, float $defaultY): array
    {
        $mapX = $record->getAttribute('map_x');
        $mapY = $record->getAttribute('map_y');

        if (filled($mapX) && filled($mapY)) {
            return [
                'x' => $this->clamp((float) $mapX),
                'y' => $this->clamp((float) $mapY),
            ];
        }

        if (
            $bounds
            && filled($record->getAttribute('latitude'))
            && filled($record->getAttribute('longitude'))
        ) {
            $latRange = max(0.0001, $bounds['max_lat'] - $bounds['min_lat']);
            $lngRange = max(0.0001, $bounds['max_lng'] - $bounds['min_lng']);

            $x = 14 + ((((float) $record->getAttribute('longitude') - $bounds['min_lng']) / $lngRange) * 72);
            $y = 14 + ((1 - (((float) $record->getAttribute('latitude') - $bounds['min_lat']) / $latRange)) * 72);

            return [
                'x' => $this->clamp(round($x, 2)),
                'y' => $this->clamp(round($y, 2)),
            ];
        }

        return [
            'x' => $this->clamp($defaultX),
            'y' => $this->clamp($defaultY),
        ];
    }

    private function categoryPriority(string $category): int
    {
        return match ($category) {
            'hotel' => 1,
            'ride' => 2,
            'game' => 3,
            'beach_event' => 4,
            'ferry' => 5,
            default => 99,
        };
    }

    private function slug(string $prefix, Model $record): string
    {
        return Str::slug($prefix.'-'.$record->getKey().'-'.$record->getAttribute('name'));
    }

    private function imageFor(array|string|null $images, string $fallback): string
    {
        if (is_array($images) && ! empty($images)) {
            return $this->normalizeImagePath($images[0]);
        }

        if (is_string($images) && filled($images)) {
            return $this->normalizeImagePath($images);
        }

        return $fallback;
    }

    private function normalizeImagePath(string $path): string
    {
        if (
            str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')
            || str_starts_with($path, '/')
        ) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    private function clamp(float $value): float
    {
        return max(6, min(94, $value));
    }
}
