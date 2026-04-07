<?php

namespace App\Support;

class HorrorGeneratedMediaCatalog
{
    public static function path(string $collection, string $slug): string
    {
        return "/generated-media/{$collection}/{$slug}.svg";
    }

    public static function entry(string $collection, string $slug): ?array
    {
        return data_get(static::catalog(), "{$collection}.{$slug}");
    }

    public static function catalog(): array
    {
        return [
            'fallbacks' => [
                'hotel' => static::entryData('Hotel', 'Horror-Bark lodging', 'manor', 'violet'),
                'room' => static::entryData('Guest Chamber', 'Lantern-lit accommodation', 'suite', 'ember'),
                'ride' => static::entryData('Ritual Ride', 'Twisted iron and velvet lights', 'coaster', 'violet'),
                'game' => static::entryData('Midnight Game', 'Carnival trials after dusk', 'game-stall', 'gold'),
                'beach-event' => static::entryData('Moonlit Gathering', 'Ceremonies along the surf', 'bonfire', 'teal'),
                'ferry' => static::entryData('Night Passage', 'Harbor crossings through black water', 'harbor', 'teal'),
                'promotion' => static::entryData('Special Invitation', 'Preferred rates under the pale moon', 'poster', 'violet'),
            ],
            'islands' => [
                'manor-ward' => static::entryData('Manor Ward', 'Cold stone promenades and watchful gates', 'manor', 'violet'),
                'shadow-park' => static::entryData('Shadow Park', 'Twisted iron rides beneath carnival lamps', 'coaster', 'violet'),
                'lantern-hollow' => static::entryData('Lantern Hollow', 'Ash paths, cedar smoke, and chapel bells', 'chapel', 'ember'),
                'blackwater-approach' => static::entryData('Blackwater Approach', 'Harbor lanterns over the black tide', 'harbor', 'teal'),
                'pale-moon-strand' => static::entryData('Pale Moon Strand', 'Moon-washed surf and ceremonial fires', 'shoreline', 'teal'),
                'saltveil-beach' => static::entryData('Saltveil Beach', 'Ash-gray sand and salt-heavy mist', 'shoreline', 'amber'),
                'coven-quay' => static::entryData('Coven Quay', 'Lantern-lined arrivals and whispered schedules', 'harbor', 'teal'),
                'blackwater-shore' => static::entryData('Blackwater Shore', 'Black surf with music carried on the mist', 'shoreline', 'ember'),
            ],
            'hotels' => [
                'the-shining-manor' => static::entryData('The Shining Manor', 'Velvet corridors and moonlit stone', 'manor', 'violet'),
                'velvet-wake-house' => static::entryData('Velvet Wake House', 'Harbor lodging for late arrivals', 'harbor', 'teal'),
                'coldstone-chambers' => static::entryData('Coldstone Chambers', 'Chapel-quarter chambers in lantern smoke', 'chapel', 'ember'),
            ],
            'rooms' => [
                'shining-north-tower-suite' => static::entryData('North Tower Suite', 'Moonlit bath and private supper service', 'suite', 'violet'),
                'shining-velvet-gallery-room' => static::entryData('Velvet Gallery Room', 'Silver service above the gallery hall', 'gallery', 'violet'),
                'wake-harbor-view-chamber' => static::entryData('Harbor View Chamber', 'Dockside breakfast above the quay', 'harbor-room', 'teal'),
                'wake-bell-tower-room' => static::entryData('Bell Tower Room', 'Sea-facing balcony beneath the bells', 'tower-room', 'teal'),
                'coldstone-lantern-cellar' => static::entryData('Lantern Cellar', 'Stone arches warmed by cedar stoves', 'cellar', 'ember'),
                'coldstone-moonfall-loft' => static::entryData('Moonfall Loft', 'A lofted family chamber by the rites', 'loft', 'amber'),
            ],
            'rides' => [
                'widows-descent' => static::entryData("Widow's Descent", 'A plunge through torn velvet and bells', 'drop-tower', 'violet'),
                'velvet-spiral' => static::entryData('Velvet Spiral', 'Polished steel wrapped in violet lamps', 'spiral', 'violet'),
                'the-ash-procession' => static::entryData('The Ash Procession', 'A solemn ride through lantern arches', 'procession', 'ember'),
                'nocturne-drop' => static::entryData('Nocturne Drop', 'A black iron fall with moonlit timing', 'drop-tower', 'teal'),
            ],
            'games' => [
                'lantern-guess' => static::entryData('Lantern Guess', 'A quiet wager under red glass', 'fortune-table', 'gold'),
                'the-silent-wheel' => static::entryData('The Silent Wheel', 'A carnival wheel with no applause', 'wheel-game', 'violet'),
                'coven-toss' => static::entryData('Coven Toss', 'Rings and sigils at the midway rail', 'game-stall', 'amber'),
                'midnight-draw' => static::entryData('Midnight Draw', 'Card tables lit by violet smoke', 'fortune-table', 'teal'),
            ],
            'beach-events' => [
                'moonlight-vigil' => static::entryData('Moonlight Vigil', 'Candles set against the turning tide', 'vigil', 'teal'),
                'the-pale-tide-gathering' => static::entryData('The Pale Tide Gathering', 'Shoreline rites around silver surf', 'shoreline', 'violet'),
                'velvet-bonfire' => static::entryData('Velvet Bonfire', 'Ceremonial fires under a salt-heavy sky', 'bonfire', 'ember'),
                'lantern-wake' => static::entryData('Lantern Wake', 'Floating lanterns and blackwater reflections', 'vigil', 'gold'),
            ],
            'promotions' => [
                'manor-midway-arrangement' => static::entryData('Manor & Midway', 'Lodging and ride privileges under one reservation', 'poster', 'violet'),
                'passage-under-the-pale-moon' => static::entryData('Passage Under The Pale Moon', 'Preferred ferry passage to the midnight shore', 'poster', 'teal'),
                'moonlit-shore-invitation' => static::entryData('Moonlit Shore Invitation', 'Preferred access to gatherings by the surf', 'poster', 'ember'),
            ],
        ];
    }

    private static function entryData(string $title, string $subtitle, string $scene, string $palette): array
    {
        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'scene' => $scene,
            'palette' => $palette,
        ];
    }
}
