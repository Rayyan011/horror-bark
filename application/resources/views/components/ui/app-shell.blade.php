@props([
    'title' => 'Horror-Bark Theme Park',
    'meta' => [],
    'theme' => 'dark',
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>

    @foreach ($meta as $name => $content)
        <meta name="{{ $name }}" content="{{ $content }}" />
    @endforeach

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet" />
    <style>
        .horror-font {
            font-family: 'Creepster', cursive;
        }
    </style>

    @livewireStyles
    @stack('head')
</head>
<body class="{{ $theme === 'light' ? 'bg-gray-100 text-gray-900' : 'bg-gray-900 text-gray-200' }} font-sans leading-normal tracking-normal">

    @isset($header)
        {{ $header }}
    @endisset

    @isset($hero)
        {{ $hero }}
    @endisset

    <div class="container mx-auto my-8 px-4">
        {{ $slot }}
    </div>

    @isset($footer)
        {{ $footer }}
    @endisset

    @livewireScripts
    @stack('scripts')
</body>
</html>
