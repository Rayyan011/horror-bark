@props([
    'title' => 'Horror-Bark Theme Park',
    'meta' => [],
    'theme' => 'dark',
])

@php
    $bodyClass = $theme === 'light'
        ? 'min-h-screen bg-zinc-100 font-sans text-zinc-900 antialiased'
        : 'min-h-screen bg-background-dark font-sans text-primary-light antialiased';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>

    @foreach ($meta as $name => $content)
        <meta name="{{ $name }}" content="{{ $content }}" />
    @endforeach

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('head')
</head>
<body class="{{ $bodyClass }}">
    <div class="relative flex min-h-screen flex-col">
        @isset($header)
            {{ $header }}
        @endisset

        @isset($hero)
            {{ $hero }}
        @endisset

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-16 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        @isset($footer)
            {{ $footer }}
        @endisset
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
