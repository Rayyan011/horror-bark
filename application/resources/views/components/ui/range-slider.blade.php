@props([
    'label',
    'name',
    'value' => null,
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'prefix' => '',
    'suffix' => '',
    'hint' => null,
    'error' => null,
    'id' => null,
])

@php
    $fieldId = $id ?: $name;
    $selectedValue = (int) old($name, $value ?? $min);
    $resolvedError = $error ?: ($errors->first($name) ?: null);
@endphp

<div
    x-data="{
        value: Number(@js($selectedValue)),
        minBound: Number(@js((int) $min)),
        maxBound: Number(@js((int) $max)),
        prefix: @js($prefix),
        suffix: @js($suffix),
        format(number) {
            return `${this.prefix}${Number(number).toLocaleString()}${this.suffix}`;
        },
    }"
    {{ $attributes->except(['class']) }}
    class="catalog-filter-field {{ $attributes->get('class') }}"
>
    <div class="catalog-range-summary">
        <label class="catalog-filter-label" for="{{ $fieldId }}">{{ $label }}</label>
        <span class="catalog-range-pill" x-text="format(value)"></span>
    </div>

    <div class="catalog-range-wrap">
        <div class="catalog-range-track"></div>
        <div
            class="catalog-range-progress"
            :style="`left: 0%; width: ${((value - minBound) / Math.max(maxBound - minBound, 1)) * 100}%`"
        ></div>
        <input
            id="{{ $fieldId }}"
            name="{{ $name }}"
            type="range"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            x-model.number="value"
            class="catalog-range-input"
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
