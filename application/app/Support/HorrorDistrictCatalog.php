<?php

namespace App\Support;

class HorrorDistrictCatalog
{
    public static function hotelLocations(): array
    {
        return self::horrorLocations();
    }

    public static function horrorLocations(): array
    {
        return [
            "Manor Ward · Keeper's Gate" => "Manor Ward · Keeper's Gate",
            'Blackwater Approach · Night Tide Dock' => 'Blackwater Approach · Night Tide Dock',
            'Lantern Hollow · Moonfall Steps' => 'Lantern Hollow · Moonfall Steps',
            'Shadow Park · Midway Grounds' => 'Shadow Park · Midway Grounds',
        ];
    }

    public static function picnicLocations(): array
    {
        return [
            'Pale Moon Strand · Saltveil Beach' => 'Pale Moon Strand · Saltveil Beach',
            'Saltveil Beach · Bonfire Reach' => 'Saltveil Beach · Bonfire Reach',
            'Coven Quay · Lantern Wake' => 'Coven Quay · Lantern Wake',
            'Blackwater Shore · Pale Tide' => 'Blackwater Shore · Pale Tide',
        ];
    }

    public static function allLocations(): array
    {
        return self::horrorLocations() + self::picnicLocations();
    }

    public static function names(): array
    {
        return [
            'Manor Ward',
            'Blackwater Approach',
            'Lantern Hollow',
            'Shadow Park',
            'Pale Moon Strand',
            'Saltveil Beach',
            'Coven Quay',
            'Blackwater Shore',
        ];
    }
}
