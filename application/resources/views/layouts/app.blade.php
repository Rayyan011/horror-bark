<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Horror-Bark Theme Park')</title> {{-- Dynamic Title using @yield and default value --}}
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <!-- Creepster font for a horror vibe -->
  <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet" />
  <style>
    .horror-font {
      font-family: 'Creepster', cursive;
    }
  </style>
  @livewireStyles
</head>
<body class="bg-gray-900 text-gray-200 font-sans leading-normal tracking-normal">
  <!-- Header -->
  <header class="bg-black text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-3xl font-bold horror-font">Horror-Bark</h1>
      <nav>
        <ul class="flex space-x-6">
          <li><a href="{{ route('home') }}" class="hover:text-gray-400">Home</a></li> {{-- Corrected Home link to use route('home') --}}
          <li><a href="{{ route('hotels.index') }}">Hotels</a></li>
          <li><a href="#" class="hover:text-gray-400">Ferry Tickets</a></li>
          <li><a href="#" class="hover:text-gray-400">Theme Park</a></li>
          <li><a href="beach-events" class="hover:text-gray-400">Beach Events</a></li>
          <li><a href="{{ url('/about') }}" class="hover:text-gray-400">About</a></li>
          <li><a href="{{ route('contacts.create') }}" class="hover:text-gray-400">Contact</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container mx-auto"> {{-- Added container to wrap page content for consistent layout --}}
    @yield('content') {{-- Content from child views will be inserted here --}}
  </div>


  <!-- Footer -->
  <footer class="bg-black text-gray-400 p-6 mt-12">
    <div class="container mx-auto text-center">
      <p>© 2025 Horror-Bark. All rights reserved.</p>
      <p class="mt-2">
        <a href="#" class="hover:text-red-400">Privacy Policy</a> |
        <a href="#" class="hover:text-red-400">Terms of Service</a>
      </p>
    </div>
  </footer>
  @livewireScripts
  <script>
    if (typeof Livewire !== 'undefined') {
      window.livewire = Livewire;
    }
  </script>
</body>
</html>