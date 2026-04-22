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

<div {{ $attributes->except(['class']) }} class="catalog-filter-field {{ $attributes->get('class') }}">
    <label class="catalog-filter-label" for="{{ $fieldId }}">{{ $label }}</label>
    <select
        id="{{ $fieldId }}"
        name="{{ $name }}"
        @if ($required) required @endif
        class="catalog-filter-control w-full border px-3 py-2 {{ $resolvedError ? 'border-rose-500' : 'border-primary-light/30' }}"
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
        <p class="mt-2 text-xs text-rose-300">{{ $resolvedError }}</p>
    @endif
</div>
