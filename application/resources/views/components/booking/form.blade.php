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
    'submitLabel' => 'Book now',
    'submitVariant' => 'primary',
    'idPrefix' => 'booking',
    'hidden' => [],
    'values' => [],
    'slotPicker' => null,
])

@php
    $quantityName = $quantityConfig['name'] ?? 'quantity';
@endphp

<x-ui.form :action="$action" :method="$method" class="space-y-3">
    @foreach ($hidden as $name => $value)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
    @endforeach

    @if ($slotPicker)
        <x-booking.slot-picker
            :entity-type="$slotPicker['type']"
            :entity-id="$slotPicker['id']"
            :input-name="$slotPicker['inputName'] ?? 'booking_time'"
            :id-prefix="$idPrefix"
        />
    @elseif ($mode === 'date-range')
        <x-ui.field
            :label="$values['start_label'] ?? 'Check-in'"
            :name="$values['start_name'] ?? 'start_date'"
            type="date"
            :value="$values['start_value'] ?? null"
            required
            :id="$idPrefix . '_start'"
        />
        <x-ui.field
            :label="$values['end_label'] ?? 'Check-out'"
            :name="$values['end_name'] ?? 'end_date'"
            type="date"
            :value="$values['end_value'] ?? null"
            required
            :id="$idPrefix . '_end'"
        />
    @elseif ($mode === 'date-time')
        <x-ui.field
            :label="$values['date_label'] ?? 'Booking date'"
            :name="$values['date_name'] ?? 'booking_date'"
            type="date"
            :value="$values['date_value'] ?? null"
            required
            :id="$idPrefix . '_date'"
        />
        <x-ui.field
            :label="$values['time_label'] ?? 'Booking time'"
            :name="$values['time_name'] ?? 'booking_time'"
            type="time"
            :value="$values['time_value'] ?? null"
            required
            :id="$idPrefix . '_time'"
        />
    @else
        <x-ui.field
            :label="$values['datetime_label'] ?? 'Booking time'"
            :name="$values['datetime_name'] ?? 'booking_time'"
            type="datetime-local"
            :value="$values['datetime_value'] ?? null"
            required
            :id="$idPrefix . '_datetime'"
        />
    @endif

    @if ($rulesHint && !$slotPicker)
        <p class="text-xs text-gray-400">{{ $rulesHint }}</p>
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
    />

    <x-ui.button type="submit" :variant="$submitVariant" block>
        {{ $submitLabel }}
    </x-ui.button>
</x-ui.form>
