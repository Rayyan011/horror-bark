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

    $inputAttributes[] = 'class="catalog-filter-control w-full border px-3 py-2 ' . e($resolvedError ? 'border-rose-500' : 'border-primary-light/30') . '"';
@endphp

<div {{ $attributes->except(['class']) }} class="catalog-filter-field {{ $attributes->get('class') }}">
    <label class="catalog-filter-label" for="{{ $fieldId }}">{{ $label }}</label>
    <input {!! implode(' ', $inputAttributes) !!} />

    @if ($hint)
        <p class="catalog-filter-hint">{{ $hint }}</p>
    @endif

    @if ($resolvedError)
        <p class="mt-2 text-xs text-rose-300">{{ $resolvedError }}</p>
    @endif
</div>
