@props([
    'success' => null,
    'error' => null,
    'warnings' => [],
    'dismissible' => false,
])

@php
    $successMessage = $success ?? session('success') ?? session('status');
    $errorMessage = $error;

    if (!$errorMessage && $errors->any()) {
        $errorMessage = $errors->first();
    }
@endphp

@if ($successMessage)
    <div class="mb-4 bg-green-700 text-white p-4 rounded {{ $dismissible ? 'relative' : '' }}">
        {{ $successMessage }}
    </div>
@endif

@if ($errorMessage)
    <div class="mb-4 bg-red-700 text-white p-4 rounded {{ $dismissible ? 'relative' : '' }}">
        {{ $errorMessage }}
    </div>
@endif

@foreach ($warnings as $warning)
    <div class="mb-4 bg-yellow-700 text-white p-4 rounded {{ $dismissible ? 'relative' : '' }}">
        {{ $warning }}
    </div>
@endforeach
