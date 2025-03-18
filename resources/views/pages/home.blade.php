<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Horror-Bark Theme Park</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <!-- Creepster font for a horror vibe -->
  <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet" />
  <style>
    .horror-font {
      font-family: 'Creepster', cursive;
    }
  </style>
</head>
<body class="bg-gray-900 text-gray-200 font-sans leading-normal tracking-normal">
  <!-- Header -->
  <header class="bg-black text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-3xl font-bold horror-font">Horror-Bark</h1>
      <nav>
        <ul class="flex space-x-6">
          <li><a href="#" class="hover:text-gray-400">Home</a></li>
          <li><a href="#" class="hover:text-gray-400">Hotels</a></li>
          <li><a href="#" class="hover:text-gray-400">Ferry Tickets</a></li>
<<<<<<< HEAD
          <li><a href="{{ route('theme-park') }}" class="hover:text-gray-400">Theme Park</a></li> 
          <li><a href="#" class="hover:text-gray-400">Beach Events</a></li>
          <li><a href="{{ route('contact') }}" class="hover:text-gray-400">Contact</a></li> 
          <li><a href="{{ route('about') }}" class="hover:text-gray-400">About Us</a>
          
=======
          <li><a href="#" class="hover:text-gray-400">Theme Park</a></li>
          <li><a href="#" class="hover:text-gray-400">Beach Events</a></li>
          <li><a href="#" class="hover:text-gray-400">Contact</a></li>
>>>>>>> 132dbe4e7633d85d880c9b2366a2ee5414c1e904
        </ul>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="bg-cover bg-center h-96" style="background-image: url('{{ asset('/storage/' . $home['hero_image']['content']) }}');">
    <div class="bg-black bg-opacity-70 h-full flex items-center justify-center">
      <div class="text-center">
        <h2 class="text-5xl font-bold mb-4 horror-font">Welcome to Horror-Bark!</h2>
        <p class="text-xl">Experience the terror, the thrills, and the unknown.</p>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <main class="container mx-auto my-8 px-4">
    <!-- Booking Requirement Notice -->
    <div class="bg-red-800 border border-red-900 text-red-300 p-4 rounded mb-8">
      <strong>Important:</strong> To enjoy any services, please book your hotel stay first!
    </div>

    <!-- Advertisements Section -->
    <section class="mb-12">
      <h3 class="text-2xl font-bold mb-4 horror-font">Featured Experiences</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Theme Park Advertisement -->
        <div class="bg-gray-800 shadow-lg rounded overflow-hidden border border-gray-700">
          <img src="https://source.unsplash.com/400x300/?rollercoaster,haunted" alt="Theme Park" class="w-full h-48 object-cover" />
          <div class="p-4">
            <h4 class="font-bold text-xl mb-2 horror-font">Horror-Bark Theme Park</h4>
            <p class="text-gray-300 text-base">
              Thrill your senses with attractions like the Space Exploration Roller Coaster and Glow-in-the-Dark Coral Ride!
            </p>
            <a href="#" class="inline-block mt-4 text-red-400 hover:underline">Learn More</a>
          </div>
        </div>
        <!-- Beach Sports Advertisement -->
        <div class="bg-gray-800 shadow-lg rounded overflow-hidden border border-gray-700">
          <img src="https://source.unsplash.com/400x300/?beach,night" alt="Beach Sports" class="w-full h-48 object-cover" />
          <div class="p-4">
            <h4 class="font-bold text-xl mb-2 horror-font">Beach Sports &amp; Events</h4>
            <p class="text-gray-300 text-base">
              Join eerie beach events on our main island – from ghostly jet skiing to moonlit paddleboarding and mysterious concerts!
            </p>
            <a href="#" class="inline-block mt-4 text-red-400 hover:underline">Discover More</a>
          </div>
        </div>
        <!-- Hotels Advertisement -->
        <div class="bg-gray-800 shadow-lg rounded overflow-hidden border border-gray-700">
          <img src="https://source.unsplash.com/400x300/?hotel,haunted" alt="Hotels" class="w-full h-48 object-cover" />
          <div class="p-4">
            <h4 class="font-bold text-xl mb-2 horror-font">Island Hotels</h4>
            <p class="text-gray-300 text-base">
              Book your stay on the island and unlock exclusive access to our spine-chilling experiences.
            </p>
            <a href="#" class="inline-block mt-4 text-red-400 hover:underline">Book Now</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="mb-12">
      <h3 class="text-2xl font-bold mb-4 horror-font">Explore the Island</h3>
      <div class="relative">
        <img src="https://source.unsplash.com/1200x600/?haunted,island" alt="Island Map" class="w-full rounded shadow-lg" />
        <!-- Example Map Markers (positioned absolutely over the image) -->
        <div class="absolute top-10 left-20 bg-red-700 text-white px-2 py-1 rounded text-xs">Hotel</div>
        <div class="absolute top-20 left-1/2 bg-green-700 text-white px-2 py-1 rounded text-xs">Theme Park</div>
        <div class="absolute top-40 right-20 bg-blue-700 text-white px-2 py-1 rounded text-xs">Beach</div>
        <div class="absolute bottom-10 left-40 bg-yellow-700 text-white px-2 py-1 rounded text-xs">Restaurants</div>
      </div>
    </section>

    <!-- Additional Attractions Section -->
    <section>
      <h3 class="text-2xl font-bold mb-4 horror-font">Other Attractions at Horror-Bark</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
          <h4 class="text-xl font-semibold mb-2 horror-font">Superhero 4D Experience</h4>
          <p class="text-gray-300">
            Step into a world where nightmares come alive! Experience our 4D simulator and join the battle against the unseen.
          </p>
        </div>
        <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
          <h4 class="text-xl font-semibold mb-2 horror-font">Haunted Maze</h4>
          <p class="text-gray-300">
            Dare to enter our twisted haunted maze. Beware—every corner hides a new terror!
          </p>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-black text-gray-400 p-6 mt-12">
    <div class="container mx-auto text-center">
      <p>&copy; 2025 Horror-Bark. All rights reserved.</p>
      <p class="mt-2">
        <a href="#" class="hover:text-red-400">Privacy Policy</a> |
        <a href="#" class="hover:text-red-400">Terms of Service</a>
      </p>
    </div>
  </footer>
</body>
</html>