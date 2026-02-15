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
    <label class="block text-sm mb-1" for="{{ $fieldId }}">{{ $label }}</label>
    <select
        id="{{ $fieldId }}"
        name="{{ $name }}"
        @if ($required) required @endif
        class="w-full px-3 py-2 rounded bg-gray-900 border {{ $resolvedError ? 'border-red-500' : 'border-gray-700' }} text-white"
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
        <p class="text-xs text-red-300 mt-1">{{ $resolvedError }}</p>
    @endif
</div>
