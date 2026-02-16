@props([
    'label',
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'id' => null,
    'error' => null,
])

@php
    $fieldId = $id ?: $name;
    $selectedValue = old($name, $value);
    $resolvedError = $error ?: ($errors->first($name) ?: null);
@endphp

<div {{ $attributes->except(['class']) }} class="{{ $attributes->get('class') }}">
    <label class="mb-1 block font-serif text-sm text-primary-light" for="{{ $fieldId }}">{{ $label }}</label>
    <select
        id="{{ $fieldId }}"
        name="{{ $name }}"
        @if ($required) required @endif
        class="w-full border bg-background-dark/90 px-3 py-2 text-moonlight focus:border-primary-light focus:outline-none {{ $resolvedError ? 'border-rose-500' : 'border-primary-light/30' }}"
    >
        @if (!is_null($placeholder))
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $option)
            @php
                $optionLabel = is_array($option) ? ($option['label'] ?? '') : $option;
                $optionValue = is_array($option) ? ($option['value'] ?? $optionLabel) : $option;
            @endphp
            <option value="{{ $optionValue }}" @selected((string) $selectedValue === (string) $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @if ($resolvedError)
        <p class="mt-1 text-xs text-rose-300">{{ $resolvedError }}</p>
    @endif
</div>
