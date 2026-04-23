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
    <label class="mb-1 block font-serif text-sm text-primary-light" for="{{ $fieldId }}">{{ $label }}</label>
    <textarea
        id="{{ $fieldId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        @if (!is_null($placeholder)) placeholder="{{ $placeholder }}" @endif
        class="w-full border bg-background-dark/90 px-3 py-2 text-moonlight placeholder:text-primary-light/70 focus:border-primary-light focus:outline-none {{ $resolvedError ? 'border-rose-500' : 'border-primary-light/30' }}"
    >{{ old($name, $value) }}</textarea>

    @if ($resolvedError)
        <p class="mt-1 text-xs text-rose-300">{{ $resolvedError }}</p>
    @endif
</div>
