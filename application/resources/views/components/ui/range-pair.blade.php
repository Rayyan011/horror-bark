@props([
    'label',
    'minName',
    'maxName',
    'minValue' => null,
    'maxValue' => null,
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'prefix' => '',
    'suffix' => '',
    'hint' => null,
    'error' => null,
])

@php
    $selectedMin = (int) old($minName, $minValue ?? $min);
    $selectedMax = (int) old($maxName, $maxValue ?? $max);
    $resolvedError = $error
        ?: ($errors->first($minName) ?: null)
        ?: ($errors->first($maxName) ?: null);
@endphp

<div
    x-data="{
        lower: Number(@js($selectedMin)),
        upper: Number(@js($selectedMax)),
        minBound: Number(@js((int) $min)),
        maxBound: Number(@js((int) $max)),
        prefix: @js($prefix),
        suffix: @js($suffix),
        format(number) {
            return `${this.prefix}${Number(number).toLocaleString()}${this.suffix}`;
        },
        syncLower() {
            if (this.lower > this.upper) {
                this.lower = this.upper;
            }
        },
        syncUpper() {
            if (this.upper < this.lower) {
                this.upper = this.lower;
            }
        },
        progressStyle() {
            const total = Math.max(this.maxBound - this.minBound, 1);
            const start = ((this.lower - this.minBound) / total) * 100;
            const width = ((this.upper - this.lower) / total) * 100;

            return `left: ${start}%; width: ${width}%`;
        },
    }"
    {{ $attributes->except(['class']) }}
    class="catalog-filter-field {{ $attributes->get('class') }}"
>
    <div class="catalog-range-summary">
        <label class="catalog-filter-label">{{ $label }}</label>
        <div class="flex flex-wrap justify-end gap-2">
            <span class="catalog-range-pill" x-text="format(lower)"></span>
            <span class="catalog-range-pill" x-text="format(upper)"></span>
        </div>
    </div>

    <div class="catalog-range-wrap">
        <div class="catalog-range-track"></div>
        <div class="catalog-range-progress" :style="progressStyle()"></div>
        <input
            name="{{ $minName }}"
            type="range"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            x-model.number="lower"
            @input="syncLower"
            class="catalog-range-input"
            aria-label="{{ $label }} minimum"
        />
        <input
            name="{{ $maxName }}"
            type="range"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            x-model.number="upper"
            @input="syncUpper"
            class="catalog-range-input"
            aria-label="{{ $label }} maximum"
        />
    </div>

    <div class="catalog-range-scale">
        <span x-text="format(minBound)"></span>
        <span x-text="format(maxBound)"></span>
    </div>

    @if ($hint)
        <p class="catalog-filter-hint">{{ $hint }}</p>
    @endif

    @if ($resolvedError)
        <p class="mt-2 text-xs text-rose-300">{{ $resolvedError }}</p>
    @endif
</div>
