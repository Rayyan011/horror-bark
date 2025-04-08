@extends('layouts.app')

@section('title', 'Home - Horror-Bark Theme Park') {{-- Optional: Set page title --}}

@section('content') {{-- Start the content section --}}
  <!-- Hero Section -->
  <section class="bg-cover bg-center h-96" style="background-image: url('{{ asset('images/default-hero.jpg') }}');">
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
          <img src="https://source.unsplash.com/random/400x300/?rollercoaster,haunted" alt="Theme Park" class="w-full h-48 object-cover" />
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
            <h4 class="font-bold text-xl mb-2 horror-font">Beach Sports & Events</h4>
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
@endsection {{-- End the content section --}}
