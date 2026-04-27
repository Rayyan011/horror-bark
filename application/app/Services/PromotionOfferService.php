<?php

namespace App\Services;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Hotel;
use App\Models\Promotion;
use App\Models\Room;
use App\Support\HorrorGeneratedMediaCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PromotionOfferService
{
    public function buildOffer(Promotion $promotion): ?array
    {
        $config = $this->configurationFor($promotion);

        if (! $config) {
            return null;
        }

        $items = match ($config['offer_type']) {
            'hotel' => $this->hotelItems($promotion, $config),
            'ferry' => $this->ferryItems($promotion, $config),
            'beach-event' => $this->beachEventItems($promotion, $config),
            default => collect(),
        };

        if ($items->isEmpty()) {
            return null;
        }

        $heroImageSource = $promotion->resolved_image_path ?: data_get($items->first(), 'image');

        return [
            'kicker' => $config['kicker'],
            'heading' => $promotion->resolved_title,
            'lede' => filled($promotion->resolved_description) ? $promotion->resolved_description : $config['lede'],
            'summary' => $config['summary'],
            'badge' => $this->discountLabel($promotion),
            'catalog_href' => $this->fallbackUrl($promotion),
            'catalog_label' => $config['catalog_label'],
            'hero_image' => $this->resolveImage($heroImageSource, HorrorGeneratedMediaCatalog::path('fallbacks', 'promotion')),
            'items' => $items,
        ];
    }

    public function resolveAppliedPromotion(null|int|string $promotionId, string $resourceType, Model $resource): ?array
    {
        if (blank($promotionId)) {
            return null;
        }

        $promotion = Promotion::query()->find($promotionId);

        if (! $promotion || ! $promotion->isLive()) {
            throw ValidationException::withMessages([
                'promotion_id' => 'That offer is no longer available.',
            ]);
        }

        $config = $this->configurationFor($promotion);

        if (! $config || $config['offer_type'] !== $resourceType) {
            throw ValidationException::withMessages([
                'promotion_id' => 'That offer does not apply to this booking.',
            ]);
        }

        $allowedIds = $this->resourceIdsFor($promotion, $config);

        if (! $allowedIds->contains((int) $resource->getKey())) {
            throw ValidationException::withMessages([
                'promotion_id' => 'That offer is not valid for the selected item.',
            ]);
        }

        return [
            'id' => $promotion->id,
            'title' => $promotion->resolved_title,
            'discount_percentage' => (float) ($promotion->discount_percentage ?? 0),
            'label' => $this->discountLabel($promotion),
        ];
    }

    public function fallbackUrl(Promotion $promotion): ?string
    {
        $ctaUrl = trim((string) $promotion->cta_url);

        if ($ctaUrl === '') {
            return null;
        }

        if (Str::startsWith($ctaUrl, ['http://', 'https://'])) {
            return $ctaUrl;
        }

        $normalizedPath = '/'.ltrim($ctaUrl, '/');

        return match ($normalizedPath) {
            '/ferries', '/ferrytickets' => route('ferries.index'),
            '/hotels' => route('hotels.index'),
            '/beach-events' => route('beach-events.index'),
            '/themepark' => route('themepark.index'),
            default => url($normalizedPath),
        };
    }

    public function discountLabel(Promotion $promotion): string
    {
        $discount = (float) ($promotion->discount_percentage ?? 0);

        return $discount > 0
            ? number_format($discount, fmod($discount, 1.0) === 0.0 ? 0 : 2).'% Off'
            : 'Featured Offer';
    }

    private function configurationFor(Promotion $promotion): ?array
    {
        $normalizedPath = $this->normalizedPath($promotion);

        if ($specificHotelConfig = $this->specificHotelConfiguration($normalizedPath)) {
            return $specificHotelConfig;
        }

        return match (true) {
            $normalizedPath === '/hotels' || Str::startsWith($normalizedPath, '/hotels/') => [
                'offer_type' => 'hotel',
                'kicker' => 'Promotion Ledger',
                'lede' => 'Choose a specific chamber from the promoted hotels and carry the reduced nightly rate straight into checkout.',
                'summary' => 'Choose from discounted rooms prepared for this offer.',
                'catalog_label' => 'Browse All Chambers',
                'hotel_names' => ['Coldstone Chambers', 'The Shining Manor', 'Velvet Wake House'],
            ],
            in_array($normalizedPath, ['/ferries', '/ferrytickets'], true) => [
                'offer_type' => 'ferry',
                'kicker' => 'Preferred Passage',
                'lede' => 'Board the black-water crossings through a proper offer page, with discounted passage applied before checkout.',
                'summary' => 'Choose a listed crossing and the promotion rate will be applied at checkout.',
                'catalog_label' => 'Browse All Ferries',
            ],
            $normalizedPath === '/beach-events' => [
                'offer_type' => 'beach-event',
                'kicker' => 'Shoreline Invitation',
                'lede' => 'Choose one of tonight’s moonlit gatherings and secure the reduced rate directly from the offer ledger.',
                'summary' => 'Each event below carries the promotion price through checkout and confirmation.',
                'catalog_label' => 'Browse All Beach Events',
            ],
            default => null,
        };
    }

    private function resourceIdsFor(Promotion $promotion, array $config): Collection
    {
        return match ($config['offer_type']) {
            'hotel' => $this->hotelRooms($config)->pluck('id'),
            'ferry' => $this->ferries()->pluck('id'),
            'beach-event' => $this->beachEvents()->pluck('id'),
            default => collect(),
        };
    }

    private function hotelItems(Promotion $promotion, array $config): Collection
    {
        return $this->hotelRooms($config)
            ->map(fn (Room $room) => [
                'type' => 'hotel',
                'eyebrow' => 'Guest Chamber',
                'title' => $room->room_number,
                'subtitle' => $room->hotel?->name ?? 'Horror-Bark lodging',
                'description' => filled($room->description) ? $room->description : 'A candlelit chamber prepared for guests who favor velvet hush over bright daylight.',
                'image' => $this->resolveImage($room->images, HorrorGeneratedMediaCatalog::path('fallbacks', 'room')),
                'meta' => [
                    ['label' => 'Hotel', 'value' => $room->hotel?->name ?? 'Horror-Bark'],
                    ['label' => 'District', 'value' => $room->hotel?->location ?? 'Manor Ward'],
                    ['label' => 'Guests', 'value' => (string) $room->max_occupancy],
                ],
                'pricing' => $this->pricingMeta((float) $room->price, (float) ($promotion->discount_percentage ?? 0), 'Nightly rate'),
                'form' => [
                    'action' => route('checkout.hotels.prepare', $room),
                    'mode' => 'date-range',
                    'rulesHint' => 'The reduced nightly rate carries through to payment review.',
                    'submitLabel' => 'Reserve discounted stay',
                    'idPrefix' => 'promotion_room_'.$room->id,
                    'hidden' => ['promotion_id' => $promotion->id],
                    'quantityConfig' => [
                        'label' => 'Guests',
                        'min' => 1,
                        'max' => $room->max_occupancy,
                        'default' => 1,
                    ],
                ],
            ])
            ->values();
    }

    private function ferryItems(Promotion $promotion, array $config): Collection
    {
        return $this->ferries()
            ->map(fn (Ferry $ferry) => [
                'type' => 'ferry',
                'eyebrow' => 'Blackwater Ferry',
                'title' => $ferry->name,
                'subtitle' => $ferry->location ?? $ferry->island?->name ?? 'Night passage',
                'description' => filled($ferry->description) ? $ferry->description : 'A lantern-run crossing through black water and harbor bells.',
                'image' => $this->resolveImage($ferry->images, HorrorGeneratedMediaCatalog::path('fallbacks', 'ferry')),
                'meta' => [
                    ['label' => 'Destination', 'value' => $ferry->location ?? $ferry->island?->name ?? 'Coven Quay'],
                    ['label' => 'Capacity', 'value' => (string) $ferry->max_capacity],
                    ['label' => 'Booking limit', 'value' => (string) $ferry->max_booking_quantity],
                ],
                'pricing' => $this->pricingMeta((float) $ferry->price, (float) ($promotion->discount_percentage ?? 0), 'Fare'),
                'form' => [
                    'action' => route('checkout.ferries.prepare', $ferry),
                    'mode' => 'datetime',
                    'rulesHint' => 'Whole hour between 9:00 and 16:00, with the offer applied before confirmation.',
                    'submitLabel' => 'Book discounted passage',
                    'idPrefix' => 'promotion_ferry_'.$ferry->id,
                    'hidden' => ['promotion_id' => $promotion->id],
                    'values' => [
                        'datetime_step' => 3600,
                    ],
                    'quantityConfig' => [
                        'label' => 'Tickets',
                        'min' => 1,
                        'max' => $ferry->max_booking_quantity,
                        'default' => 1,
                    ],
                ],
            ])
            ->values();
    }

    private function beachEventItems(Promotion $promotion, array $config): Collection
    {
        return $this->beachEvents()
            ->map(fn (BeachEvent $event) => [
                'type' => 'beach-event',
                'eyebrow' => 'Moonlit Gathering',
                'title' => $event->name,
                'subtitle' => optional($event->event_date)->format('F d, Y') ?: 'Tonight',
                'description' => filled($event->description) ? $event->description : 'A shoreline gathering arranged under silver surf and ceremonial firelight.',
                'image' => $this->resolveImage($event->images, HorrorGeneratedMediaCatalog::path('fallbacks', 'beach-event')),
                'meta' => [
                    ['label' => 'District', 'value' => $event->location ?? $event->island?->name ?? 'Picnic Island'],
                    ['label' => 'Organizer', 'value' => $event->owner?->name ?? 'Event host'],
                    ['label' => 'Capacity', 'value' => (string) $event->max_capacity],
                ],
                'pricing' => $this->pricingMeta((float) $event->price, (float) ($promotion->discount_percentage ?? 0), 'Entry ticket'),
                'form' => [
                    'action' => route('checkout.beach-events.prepare', $event),
                    'mode' => 'date-time',
                    'rulesHint' => 'The event date is pre-filled and the offer continues through checkout.',
                    'submitLabel' => 'Book discounted event',
                    'idPrefix' => 'promotion_event_'.$event->id,
                    'hidden' => ['promotion_id' => $promotion->id],
                    'quantityConfig' => [
                        'label' => 'Tickets',
                        'min' => 1,
                        'max' => $event->max_booking_quantity,
                        'default' => 1,
                    ],
                    'values' => [
                        'date_value' => $event->event_date,
                    ],
                ],
            ])
            ->values();
    }

    private function hotelRooms(array $config): Collection
    {
        $query = Room::query()
            ->with('hotel')
            ->where('status', 'available');

        if (filled($config['hotel_ids'] ?? [])) {
            $query->whereIn('hotel_id', $config['hotel_ids']);
        } elseif (filled($config['hotel_names'] ?? [])) {
            $query->whereHas('hotel', fn ($builder) => $builder->whereIn('name', $config['hotel_names']));
        } else {
            return collect();
        }

        return $query
            ->orderBy('hotel_id')
            ->orderBy('price')
            ->orderBy('room_number')
            ->get()
            ->values();
    }

    private function normalizedPath(Promotion $promotion): string
    {
        $path = '/'.ltrim(parse_url((string) $promotion->cta_url, PHP_URL_PATH) ?: '', '/');

        return $path !== '/' ? rtrim($path, '/') : $path;
    }

    private function specificHotelConfiguration(string $normalizedPath): ?array
    {
        if (! preg_match('#^/hotels/(?P<hotel>\d+)$#', $normalizedPath, $matches)) {
            return null;
        }

        $hotel = Hotel::query()->find((int) $matches['hotel']);

        if (! $hotel) {
            return null;
        }

        return [
            'offer_type' => 'hotel',
            'kicker' => 'Promotion Ledger',
            'lede' => $hotel->name.' offers discounted rooms prepared for direct booking from this page.',
            'summary' => 'Choose one of the eligible rooms below and the reduced nightly rate will stay attached through checkout and confirmation.',
            'catalog_label' => 'View '.$hotel->name,
            'hotel_ids' => [$hotel->id],
        ];
    }

    private function ferries(): Collection
    {
        return Ferry::query()
            ->with('island')
            ->orderBy('price')
            ->orderBy('name')
            ->limit(3)
            ->get();
    }

    private function beachEvents(): Collection
    {
        return BeachEvent::query()
            ->with('island', 'owner')
            ->orderBy('event_date')
            ->orderBy('name')
            ->limit(3)
            ->get();
    }

    private function pricingMeta(float $basePrice, float $discountPercentage, string $unitLabel): array
    {
        $discountedPrice = $this->applyDiscount($basePrice, $discountPercentage);

        return [
            'unit_label' => $unitLabel,
            'base_price' => round($basePrice, 2),
            'discounted_price' => $discountedPrice,
            'savings' => round(max(0, $basePrice - $discountedPrice), 2),
            'discount_label' => $discountPercentage > 0
                ? number_format($discountPercentage, fmod($discountPercentage, 1.0) === 0.0 ? 0 : 2).'% Off'
                : 'Featured Offer',
        ];
    }

    private function applyDiscount(float $price, float $discountPercentage): float
    {
        if ($discountPercentage <= 0) {
            return round($price, 2);
        }

        return round($price * (1 - ($discountPercentage / 100)), 2);
    }

    private function resolveImage(array|string|null $images, string $fallback): string
    {
        $image = collect(is_array($images) ? $images : [$images])
            ->first(fn ($value) => filled($value));

        if (is_string($image) && $image !== '') {
            if (Str::startsWith($image, ['http://', 'https://', '/'])) {
                return $image;
            }

            return asset('storage/'.ltrim($image, '/'));
        }

        return Str::startsWith($fallback, ['http://', 'https://', '/'])
            ? $fallback
            : asset($fallback);
    }
}
