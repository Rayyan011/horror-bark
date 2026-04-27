@props([
    'label' => 'Date range',
    'startName' => 'start_date',
    'endName' => 'end_date',
    'startLabel' => 'From',
    'endLabel' => 'To',
    'startValue' => null,
    'endValue' => null,
    'startMin' => null,
    'endMin' => null,
    'startMax' => null,
    'endMax' => null,
    'hint' => null,
    'idPrefix' => 'date_range',
    'showError' => true,
    'useOldValue' => true,
])

@php
    $startId = $idPrefix . '_start';
    $endId = $idPrefix . '_end';
    $resolvedStartValue = $useOldValue ? old($startName, $startValue) : $startValue;
    $resolvedEndValue = $useOldValue ? old($endName, $endValue) : $endValue;
    $resolvedStartMin = $startMin ?? now()->toDateString();
    $resolvedEndMin = $endMin ?? now()->addDay()->toDateString();
    $resolvedError = $showError
        ? (($errors->first($startName) ?: null) ?: ($errors->first($endName) ?: null))
        : null;
@endphp

<div
    x-data="{
        start: @js($resolvedStartValue ?: ''),
        end: @js($resolvedEndValue ?: ''),
        configuredEndMin: @js($resolvedEndMin),
        nextDay(value) {
            if (!value) {
                return this.configuredEndMin;
            }

            const date = new Date(`${value}T00:00:00`);
            date.setDate(date.getDate() + 1);

            return date.toISOString().slice(0, 10);
        },
        get endMinimum() {
            return this.start ? this.nextDay(this.start) : this.configuredEndMin;
        },
        syncEnd() {
            if (this.end && this.end < this.endMinimum) {
                this.end = '';
            }
        },
        rangeLabel() {
            if (!this.start && !this.end) {
                return 'Select dates';
            }

            return `${this.start || 'From'} - ${this.end || 'To'}`;
        },
    }"
    {{ $attributes->except(['class']) }}
    class="catalog-filter-field {{ $attributes->get('class') }}"
>
    <div class="catalog-range-summary">
        <label class="catalog-filter-label">{{ $label }}</label>
        <span class="catalog-range-pill" x-text="rangeLabel()"></span>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <div class="grid gap-2">
            <label class="catalog-filter-label" for="{{ $startId }}">{{ $startLabel }}</label>
            <input
                id="{{ $startId }}"
                name="{{ $startName }}"
                type="date"
                min="{{ $resolvedStartMin }}"
                @if (! is_null($startMax)) max="{{ $startMax }}" @endif
                x-model="start"
                @change="syncEnd"
                required
                class="catalog-filter-control w-full border px-3 py-2 {{ $resolvedError ? 'border-rose-500' : 'border-primary-light/30' }}"
            />
        </div>

        <div class="grid gap-2">
            <label class="catalog-filter-label" for="{{ $endId }}">{{ $endLabel }}</label>
            <input
                id="{{ $endId }}"
                name="{{ $endName }}"
                type="date"
                :min="endMinimum"
                @if (! is_null($endMax)) max="{{ $endMax }}" @endif
                x-model="end"
                required
                class="catalog-filter-control w-full border px-3 py-2 {{ $resolvedError ? 'border-rose-500' : 'border-primary-light/30' }}"
            />
        </div>
    </div>

    @if ($hint)
        <p class="catalog-filter-hint">{{ $hint }}</p>
    @endif

    @if ($resolvedError)
        <p class="mt-2 text-xs text-rose-300">{{ $resolvedError }}</p>
    @endif
</div>
