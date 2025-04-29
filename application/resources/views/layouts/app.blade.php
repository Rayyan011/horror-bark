<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Horror-Bark Theme Park')</title> {{-- Dynamic Title using @yield and default value --}}
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet" />
  <style>
    .horror-font {
      font-family: 'Creepster', cursive;
    }
  </style>
  @livewireStyles
</head>
<body class="bg-gray-900 text-gray-200 font-sans leading-normal tracking-normal">

  <header class="bg-black text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      {{-- Clickable Header Logo --}}
      <a href="{{ route('home') }}">
          <h1 class="text-3xl font-bold horror-font">Horror-Bark</h1>
      </a>
      <nav>
        {{-- Updated Navigation Links --}}
        <ul class="flex space-x-6">
          <li><a href="{{ route('home') }}" class="hover:text-gray-400">Home</a></li>
          <li><a href="{{ route('hotels.index') }}" class="hover:text-gray-400">Hotels</a></li>
          <li><a href="{{ route('ferries.index') }}" class="hover:text-gray-400">Ferry Tickets</a></li>
          <li><a href="#" class="hover:text-gray-400">Theme Park</a></li> {{-- Placeholder link --}}
          <li><a href="{{ route('beach-events.index') }}" class="hover:text-gray-400">Beach Events</a></li>
          <li><a href="{{ route('contacts.create') }}" class="hover:text-gray-400">Contact</a></li>
          <li><a href="{{ url('/about') }}" class="hover:text-gray-400">About</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="bg-cover bg-center h-72" style="background-image: url('{{ asset('images/banner.webp') }}');"> {{-- <-- UPDATED IMAGE PATH --}}
      <div class="bg-black bg-opacity-60 h-full flex items-center justify-center">
          <div class="text-center text-white">
               <h2 class="text-4xl font-bold mb-4 horror-font">Welcome to Horror-Bark</h2> {{-- Example Static Text --}}
               {{-- Or use: @yield('banner-content') if needed --}}
          </div>
      </div>
  </section>
  {{-- Main Content Area --}}
  <div class="container mx-auto my-8 px-4"> {{-- Added padding/margin around content --}}
    @yield('content') {{-- Content from child views will be inserted here --}}
  </div>

  <footer class="bg-black text-gray-400 p-6 mt-12">
    <div class="container mx-auto text-center">
      <p>© {{ date('Y') }} Horror-Bark. All rights reserved.</p> {{-- Use dynamic year --}}
      <p class="mt-2">
        <a href="#" class="hover:text-red-400">Privacy Policy</a> |
        <a href="#" class="hover:text-red-400">Terms of Service</a>
      </p>
    </div>
  </footer>

  @livewireScripts
  <script>
    // Optional: Add check for Livewire existence if needed elsewhere
    // if (typeof Livewire !== 'undefined') {
    //   window.livewire = Livewire;
    // }
  </script>
</body>
</html>