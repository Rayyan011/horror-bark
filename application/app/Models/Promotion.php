<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Promotion extends Model
{
    protected $fillable = [
        'title',
        'description',
        'discount_percentage',
        'cta_label',
        'cta_url',
        'image_path',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query, ?Carbon $at = null): Builder
    {
        $at ??= now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder) use ($at) {
                $builder->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $at);
            })
            ->where(function (Builder $builder) use ($at) {
                $builder->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $at);
            });
    }

    public function getResolvedCtaLabelAttribute(): string
    {
        return $this->presentationDefaults()['cta_label']
            ?? $this->cta_label
            ?: 'View Offer';
    }

    public function getResolvedTitleAttribute(): string
    {
        return $this->presentationDefaults()['title'] ?? $this->title;
    }

    public function getResolvedDescriptionAttribute(): string
    {
        return $this->presentationDefaults()['description'] ?? $this->description;
    }

    public function getResolvedImagePathAttribute(): ?string
    {
        $imagePath = $this->presentationDefaults()['image_path'] ?? $this->image_path;

        if (! filled($imagePath)) {
            return null;
        }

        if (preg_match('#^/generated-media/(?P<collection>[^/]+)/(?P<slug>[^/]+)\.svg$#', $imagePath, $matches) === 1) {
            $candidate = $matches['collection'].'/gallery/'.$matches['slug'].'-01.png';

            if (file_exists(storage_path('app/public/'.$candidate))) {
                return $candidate;
            }
        }

        return $imagePath;
    }

    public function isLive(?Carbon $at = null): bool
    {
        $at ??= now();

        return (bool) $this->is_active
            && (! $this->starts_at || $this->starts_at->lte($at))
            && (! $this->ends_at || $this->ends_at->gte($at));
    }

    private function presentationDefaults(): array
    {
        return match ($this->title) {
            'Manor & Midway Arrangement' => [
                'title' => 'Moonlit Chamber Rates',
                'description' => 'Discounted chamber stays across the manor quarter, carried straight into checkout without bundling in unrelated activities.',
                'cta_label' => 'View Discount',
                'image_path' => 'rooms/gallery/shining-velvet-gallery-room-01.png',
            ],
            'Passage Under The Pale Moon' => [
                'title' => 'Moonwake Ferry Discount',
                'description' => 'Reduced ferry fares on selected black-water crossings, booked directly from the discount page.',
                'cta_label' => 'View Discount',
                'image_path' => 'ferries/gallery/moonwake-line-01.png',
            ],
            'Moonlit Shore Invitation' => [
                'title' => 'Moonlit Shore Event Discount',
                'description' => 'Reduced admission on selected shoreline gatherings, with the discounted price preserved through checkout.',
                'cta_label' => 'View Discount',
                'image_path' => 'beach-events/gallery/moonlight-vigil-01.png',
            ],
            default => [],
        };
    }
}
