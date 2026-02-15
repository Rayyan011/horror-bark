@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'id' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'placeholder' => null,
])

@php
    $fieldId = $id ?: $name;
    $resolvedError = $error ?: ($errors->first($name) ?: null);
    $inputAttributes = [
        'id="' . e($fieldId) . '"',
        'name="' . e($name) . '"',
        'type="' . e($type) . '"',
    ];

    if (!is_null($min)) {
        $inputAttributes[] = 'min="' . e($min) . '"';
    }

    if (!is_null($max)) {
        $inputAttributes[] = 'max="' . e($max) . '"';
    }

    if (!is_null($step)) {
        $inputAttributes[] = 'step="' . e($step) . '"';
    }

    $inputAttributes[] = 'value="' . e(old($name, $value)) . '"';

    if ($required) {
        $inputAttributes[] = 'required';
    }

    if (!is_null($placeholder)) {
        $inputAttributes[] = 'placeholder="' . e($placeholder) . '"';
    }

    $inputAttributes[] = 'class="w-full px-3 py-2 rounded bg-gray-900 border ' . e($resolvedError ? 'border-red-500' : 'border-gray-700') . ' text-white"';
@endphp

<div {{ $attributes->except(['class']) }} class="{{ $attributes->get('class') }}">
    <label class="block text-sm mb-1" for="{{ $fieldId }}">{{ $label }}</label>
    <input {!! implode(' ', $inputAttributes) !!} />

    @if ($hint)
        <p class="text-xs text-gray-400 mt-1">{{ $hint }}</p>
    @endif

    @if ($resolvedError)
        <p class="text-xs text-red-300 mt-1">{{ $resolvedError }}</p>
    @endif
</div>
