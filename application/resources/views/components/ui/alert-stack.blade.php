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
    <div class="theme-success-alert {{ $dismissible ? 'relative' : '' }}">
        {{ $successMessage }}
    </div>
@endif

@if ($errorMessage)
    <div class="theme-error-alert {{ $dismissible ? 'relative' : '' }}">
        {{ $errorMessage }}
    </div>
@endif

@foreach ($warnings as $warning)
    <div class="theme-warning-alert {{ $dismissible ? 'relative' : '' }}">
        {{ $warning }}
    </div>
@endforeach
