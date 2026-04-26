<?php

namespace App\Support;

use App\Services\IslandAccessService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class IslandTypeCatalogFilter
{
    public static function rule()
    {
        return Rule::in([
            IslandAccessService::HORROR_ISLAND,
            IslandAccessService::PICNIC_ISLAND,
        ]);
    }

    public static function options(): array
    {
        return [
            ['label' => 'All Island Types', 'value' => ''],
            ['label' => 'Horror Island', 'value' => IslandAccessService::HORROR_ISLAND],
            ['label' => 'Picnic Island', 'value' => IslandAccessService::PICNIC_ISLAND],
        ];
    }

    public static function apply(Builder $query, ?string $islandType, string $nullFallbackType): void
    {
        if (empty($islandType)) {
            return;
        }

        $query->where(function (Builder $builder) use ($islandType, $nullFallbackType) {
            $builder->whereHas('island', function (Builder $islandQuery) use ($islandType) {
                $islandQuery->where('type', $islandType);
            });

            if ($islandType === $nullFallbackType) {
                $builder->orWhereNull('island_id');
            }
        });
    }
}
