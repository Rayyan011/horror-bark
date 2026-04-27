@props([
    'action',
    'method' => 'POST',
    'mode' => 'datetime',
    'quantityConfig' => [
        'name' => 'quantity',
        'label' => 'Quantity',
        'min' => 1,
        'max' => 1,
        'default' => 1,
    ],
    'rulesHint' => null,
    'submitLabel' => 'Review & pay',
    'submitVariant' => 'primary',
    'idPrefix' => 'booking',
    'hidden' => [],
    'values' => [],
])

@php
    $quantityName = $quantityConfig['name'] ?? 'quantity';
    $formId = $values['form_id'] ?? $idPrefix;
    $formIdName = '_booking_form_id';
    $showFieldErrors = old($formIdName) === $formId;
@endphp

<x-ui.form :action="$action" :method="$method" class="space-y-3">
    <input type="hidden" name="{{ $formIdName }}" value="{{ $formId }}" />

    @foreach ($hidden as $name => $value)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
    @endforeach

    @if ($mode === 'date-range')
        <x-ui.date-range
            :label="$values['range_label'] ?? 'Stay dates'"
            :start-name="$values['start_name'] ?? 'start_date'"
            :end-name="$values['end_name'] ?? 'end_date'"
            :start-label="$values['start_label'] ?? 'From'"
            :end-label="$values['end_label'] ?? 'To'"
            :start-value="$values['start_value'] ?? null"
            :end-value="$values['end_value'] ?? null"
            :start-min="$values['start_min'] ?? now()->toDateString()"
            :end-min="$values['end_min'] ?? now()->addDay()->toDateString()"
            :start-max="$values['start_max'] ?? null"
            :end-max="$values['end_max'] ?? null"
            :hint="$values['range_hint'] ?? null"
            :id-prefix="$idPrefix . '_range'"
            :show-error="$showFieldErrors"
            :use-old-value="$showFieldErrors"
        />
    @elseif ($mode === 'date-time')
        <x-ui.field
            :label="$values['date_label'] ?? 'Booking date'"
            :name="$values['date_name'] ?? 'booking_date'"
            type="date"
            :value="$values['date_value'] ?? null"
            :min="$values['date_min'] ?? null"
            :max="$values['date_max'] ?? null"
            required
            :id="$idPrefix . '_date'"
            :show-error="$showFieldErrors"
            :use-old-value="$showFieldErrors"
        />
        <x-ui.field
            :label="$values['time_label'] ?? 'Booking time'"
            :name="$values['time_name'] ?? 'booking_time'"
            type="time"
            :value="$values['time_value'] ?? null"
            :min="$values['time_min'] ?? null"
            :max="$values['time_max'] ?? null"
            :step="$values['time_step'] ?? null"
            required
            :id="$idPrefix . '_time'"
            :show-error="$showFieldErrors"
            :use-old-value="$showFieldErrors"
        />
    @else
        <x-ui.field
            :label="$values['datetime_label'] ?? 'Booking time'"
            :name="$values['datetime_name'] ?? 'booking_time'"
            type="datetime-local"
            :value="$values['datetime_value'] ?? null"
            :min="$values['datetime_min'] ?? null"
            :max="$values['datetime_max'] ?? null"
            :step="$values['datetime_step'] ?? null"
            required
            :id="$idPrefix . '_datetime'"
            :show-error="$showFieldErrors"
            :use-old-value="$showFieldErrors"
        />
    @endif

    @if ($rulesHint)
        <p class="catalog-filter-hint">{{ $rulesHint }}</p>
    @endif

    <x-ui.field
        :label="$quantityConfig['label'] ?? 'Quantity'"
        :name="$quantityName"
        type="number"
        :min="$quantityConfig['min'] ?? 1"
        :max="$quantityConfig['max'] ?? null"
        :value="$quantityConfig['default'] ?? 1"
        required
        :id="$idPrefix . '_quantity'"
        :show-error="$showFieldErrors"
        :use-old-value="$showFieldErrors"
    />

    <x-ui.button type="submit" :variant="$submitVariant" block>
        {{ $submitLabel }}
    </x-ui.button>
</x-ui.form>
