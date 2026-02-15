@props([
    'label',
    'name',
    'rows' => 4,
    'value' => null,
    'required' => false,
    'id' => null,
    'placeholder' => null,
    'error' => null,
])

@php
    $fieldId = $id ?: $name;
    $resolvedError = $error ?: ($errors->first($name) ?: null);
@endphp

<div {{ $attributes->except(['class']) }} class="{{ $attributes->get('class') }}">
    <label class="block text-sm mb-1" for="{{ $fieldId }}">{{ $label }}</label>
    <textarea
        id="{{ $fieldId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        @if (!is_null($placeholder)) placeholder="{{ $placeholder }}" @endif
        class="w-full px-3 py-2 rounded bg-gray-900 border {{ $resolvedError ? 'border-red-500' : 'border-gray-700' }} text-white"
    >{{ old($name, $value) }}</textarea>

    @if ($resolvedError)
        <p class="text-xs text-red-300 mt-1">{{ $resolvedError }}</p>
    @endif
</div>
