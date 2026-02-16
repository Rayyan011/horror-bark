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

    $inputAttributes[] = 'class="w-full border bg-background-dark/90 px-3 py-2 text-moonlight placeholder:text-primary-light/60 focus:border-primary-light focus:outline-none ' . e($resolvedError ? 'border-rose-500' : 'border-primary-light/30') . '"';
@endphp

<div {{ $attributes->except(['class']) }} class="{{ $attributes->get('class') }}">
    <label class="mb-1 block font-serif text-sm text-primary-light" for="{{ $fieldId }}">{{ $label }}</label>
    <input {!! implode(' ', $inputAttributes) !!} />

    @if ($hint)
        <p class="mt-1 text-xs text-primary-light/80">{{ $hint }}</p>
    @endif

    @if ($resolvedError)
        <p class="mt-1 text-xs text-rose-300">{{ $resolvedError }}</p>
    @endif
</div>
