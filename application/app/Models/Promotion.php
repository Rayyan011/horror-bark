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
        return $this->cta_label ?: 'View Offer';
    }
}
