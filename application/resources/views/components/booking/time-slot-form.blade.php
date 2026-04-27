@props([
    'action',
    'method' => 'POST',
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
    'timeOptions' => ['09:00', '17:00'],
    'dateOptions' => [],
    'dateMin' => null,
    'dateMax' => null,
    'dateValue' => null,
    'dateLabel' => 'Date',
    'timeLabel' => 'Time',
    'requiresHotel' => false,
    'hotelStayWindows' => [],
    'disabled' => false,
    'disabledReason' => null,
    'invalidDateMessage' => 'Choose a date during your confirmed hotel stay.',
    'futureMessage' => 'Choose a future time.',
    'combinedFieldName' => 'booking_time',
    'submitDateName' => null,
    'submitTimeName' => null,
    'dateFieldName' => '_booking_date',
    'timeFieldName' => '_booking_hour',
])

@php
    $quantityName = $quantityConfig['name'] ?? 'quantity';
    $resolvedTimeOptions = $timeOptions ?: ['09:00', '17:00'];
    $formIdName = '_booking_form_id';
    $showFieldErrors = old($formIdName) === $idPrefix;
    $oldBookingTime = $combinedFieldName ? old($combinedFieldName) : null;
    $oldBookingDate = old($dateFieldName) ?: ($submitDateName ? old($submitDateName) : null);
    $oldBookingHour = old($timeFieldName) ?: ($submitTimeName ? old($submitTimeName) : null);

    if ($oldBookingTime && (! $oldBookingDate || ! $oldBookingHour)) {
        try {
            $parsedBookingTime = \Illuminate\Support\Carbon::parse($oldBookingTime);
            $oldBookingDate ??= $parsedBookingTime->toDateString();
            $oldBookingHour ??= $parsedBookingTime->format('H:i');
        } catch (\Throwable) {
            //
        }
    }

    $resolvedDateOptions = $dateOptions ?: [];
    $defaultDate = $oldBookingDate ?: ($dateValue ?: (count($resolvedDateOptions) === 1 ? $resolvedDateOptions[0]['value'] : ''));
    $resolvedDateMin = $dateMin ?? now()->toDateString();
    $currentDate = now()->toDateString();
    $currentTime = now()->format('H:i');
    $bookingTimeError = $showFieldErrors
        ? (($combinedFieldName ? ($errors->first($combinedFieldName) ?: null) : null)
            ?: ($submitDateName ? ($errors->first($submitDateName) ?: null) : null)
            ?: ($submitTimeName ? ($errors->first($submitTimeName) ?: null) : null))
        : null;
    $quantityError = $showFieldErrors ? ($errors->first($quantityName) ?: null) : null;
@endphp

<x-ui.form
    :action="$action"
    :method="$method"
    class="space-y-3"
    x-data="{
        date: @js($defaultDate),
        time: @js($oldBookingHour ?: ($resolvedTimeOptions[0] ?? '09:00')),
        windows: @js($hotelStayWindows),
        requiresHotel: @js((bool) $requiresHotel),
        blocked: @js((bool) $disabled),
        dateMin: @js($resolvedDateMin),
        dateMax: @js($dateMax),
        currentDate: @js($currentDate),
        currentTime: @js($currentTime),
        isInsideDateBounds(value) {
            return Boolean(value) && value >= this.dateMin && (!this.dateMax || value <= this.dateMax);
        },
        isDateAllowed(value) {
            if (this.blocked || !this.isInsideDateBounds(value)) {
                return false;
            }

            if (!this.requiresHotel) {
                return true;
            }

            return this.windows.some((window) => value >= window.start && value <= window.end);
        },
        isFutureTime() {
            return this.date !== this.currentDate || this.time > this.currentTime;
        },
        combinedBookingTime() {
            if (!this.isDateAllowed(this.date) || !this.time || !this.isFutureTime()) {
                return '';
            }

            return `${this.date} ${this.time}`;
        },
        canSubmit() {
            return !this.blocked && this.isDateAllowed(this.date) && Boolean(this.time) && this.isFutureTime();
        },
        selectedDateMessage() {
            if (this.blocked) {
                return @js($disabledReason);
            }

            if (this.date && this.time && !this.isFutureTime()) {
                return @js($futureMessage);
            }

            if (!this.requiresHotel || !this.date || this.isDateAllowed(this.date)) {
                return '';
            }

            return @js($invalidDateMessage);
        },
    }"
>
    <input type="hidden" name="{{ $formIdName }}" value="{{ $idPrefix }}" />
    @if ($combinedFieldName)
        <input type="hidden" name="{{ $combinedFieldName }}" :value="combinedBookingTime()" />
    @endif
    @if ($submitDateName)
        <input type="hidden" name="{{ $submitDateName }}" :value="canSubmit() ? date : ''" />
    @endif
    @if ($submitTimeName)
        <input type="hidden" name="{{ $submitTimeName }}" :value="canSubmit() ? time : ''" />
    @endif

    <div class="catalog-filter-field">
        <div class="catalog-range-summary">
            <label class="catalog-filter-label" for="{{ $idPrefix }}_date">{{ $dateLabel }}</label>
            <span class="catalog-range-pill" x-text="date && time ? `${date} ${time}` : 'Select date and time'"></span>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="grid gap-2">
                <label class="catalog-filter-label" for="{{ $idPrefix }}_date">{{ $dateLabel }}</label>
                @if (count($resolvedDateOptions) > 0)
                    <select
                        id="{{ $idPrefix }}_date"
                        name="{{ $dateFieldName }}"
                        x-model="date"
                        :disabled="blocked"
                        required
                        class="catalog-filter-control w-full border px-3 py-2 {{ $bookingTimeError ? 'border-rose-500' : 'border-primary-light/30' }}"
                    >
                        <option value="">Select date</option>
                        @foreach ($resolvedDateOptions as $option)
                            <option value="{{ $option['value'] }}" @selected($defaultDate === $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                @else
                    <input
                        id="{{ $idPrefix }}_date"
                        name="{{ $dateFieldName }}"
                        type="date"
                        min="{{ $resolvedDateMin }}"
                        @if ($dateMax) max="{{ $dateMax }}" @endif
                        x-model="date"
                        value="{{ $defaultDate }}"
                        :disabled="blocked"
                        required
                        class="catalog-filter-control w-full border px-3 py-2 {{ $bookingTimeError ? 'border-rose-500' : 'border-primary-light/30' }}"
                    />
                @endif
            </div>

            <div class="grid gap-2">
                <label class="catalog-filter-label" for="{{ $idPrefix }}_time">{{ $timeLabel }}</label>
                <select
                    id="{{ $idPrefix }}_time"
                    name="{{ $timeFieldName }}"
                    x-model="time"
                    :disabled="blocked"
                    required
                    class="catalog-filter-control w-full border px-3 py-2 {{ $bookingTimeError ? 'border-rose-500' : 'border-primary-light/30' }}"
                >
                    @foreach ($resolvedTimeOptions as $timeOption)
                        <option
                            value="{{ $timeOption }}"
                            @selected($oldBookingHour === $timeOption)
                            x-bind:disabled="date === currentDate && '{{ $timeOption }}' <= currentTime"
                        >
                            {{ $timeOption }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($requiresHotel && count($hotelStayWindows) > 0)
            <div class="flex flex-wrap gap-2">
                @foreach ($hotelStayWindows as $window)
                    <span class="catalog-range-pill">{{ $window['label'] }}</span>
                @endforeach
            </div>
        @endif

        <p class="catalog-filter-hint" x-show="selectedDateMessage()" x-text="selectedDateMessage()">{{ $disabled && $disabledReason ? $disabledReason : '' }}</p>

        @if ($rulesHint)
            <p class="catalog-filter-hint">{{ $rulesHint }}</p>
        @endif

        @if ($bookingTimeError)
            <p class="mt-2 text-xs text-rose-300">{{ $bookingTimeError }}</p>
        @endif
    </div>

    <div class="catalog-filter-field">
        <label class="catalog-filter-label" for="{{ $idPrefix }}_quantity">{{ $quantityConfig['label'] ?? 'Quantity' }}</label>
        <input
            id="{{ $idPrefix }}_quantity"
            name="{{ $quantityName }}"
            type="number"
            min="{{ $quantityConfig['min'] ?? 1 }}"
            @if (! is_null($quantityConfig['max'] ?? null)) max="{{ $quantityConfig['max'] }}" @endif
            value="{{ old($quantityName, $quantityConfig['default'] ?? 1) }}"
            required
            x-bind:disabled="blocked"
            class="catalog-filter-control w-full border px-3 py-2 {{ $quantityError ? 'border-rose-500' : 'border-primary-light/30' }}"
        />

        @if ($quantityError)
            <p class="mt-2 text-xs text-rose-300">{{ $quantityError }}</p>
        @endif
    </div>

    <x-ui.button
        type="submit"
        :variant="$submitVariant"
        block
        x-bind:disabled="!canSubmit()"
        x-bind:class="!canSubmit() ? 'cursor-not-allowed opacity-45' : ''"
    >
        {{ $submitLabel }}
    </x-ui.button>
</x-ui.form>
