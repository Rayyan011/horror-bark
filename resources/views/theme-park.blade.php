<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Theme Park - Horror-Bark Theme Park</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet" />
    <style>
        .horror-font {
            font-family: 'Creepster', cursive;
        }
    </style>
</head>
<body class="antialiased bg-gray-900 text-gray-200">
    <header class="bg-black text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold horror-font">Horror-Bark</h1>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="{{ route('home') }}" class="hover:text-gray-400">Home</a></li>
                    <li><a href="#" class="hover:text-gray-400">Hotels</a></li>
                    <li><a href="#" class="hover:text-gray-400">Ferry Tickets</a></li>
                    <li><a href="{{ route('theme-park') }}" class="hover:text-gray-400">Theme Park</a></li>
                    <li><a href="#" class="hover:text-gray-400">Beach Events</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-gray-400">Contact</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-gray-400">About Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold **text-red-500** horror-font mb-6 text-center">Horror-Bark Theme Park - Attractions</h1> <!-- Main Heading - CHANGED TO RED -->

        <div class="bg-gray-800 shadow-md rounded-lg p-8">
            <p class="text-gray-300 leading-relaxed mb-8 text-center">
                Prepare to descend into delightful darkness! Horror-Bark Theme Park is not for the faint of heart. Explore our terrifying attractions, each designed to chill you to the bone and leave you screaming for more! Book your tickets now, if you dare...
            </p>

            <div class="mt-8">
                <h2 class="text-2xl font-bold **text-red-400** horror-font mb-6 text-center">Featured Attractions</h2> <!-- Subheading - CHANGED TO RED -->

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    <!-- Space Exploration Roller Coaster -->
                    <div class="mb-6">
                        <h3 class="text-red-500 font-bold horror-font mb-2 text-center">Space Exploration Roller Coaster</h3>
                        <div class="relative">
                            <img src="https://source.unsplash.com/400x300/?rollercoaster,space,night" alt="Space Exploration Roller Coaster" class="w-full h-64 object-cover rounded-lg shadow-md">
                        </div>
                        <p class="text-red-500 horror-font leading-relaxed mt-3">
                            <span class="font-semibold text-red-300">Dare to launch</span> into the abyssal void of space! This high-speed coaster plunges you through nebulae and asteroid fields, encountering cosmic horrors and unexpected gravity drops. Feel the chilling vacuum of space whip past as you scream through the darkness!
                        </p>
                    </div>

                    <!-- Glow-in-the-Dark Coral Ride -->
                    <div class="mb-6">
                        <h3 class="text-red-500 font-bold horror-font mb-2 text-center">Glow-in-the-Dark Coral Ride</h3>
                        <div class="relative">
                            <img src="https://source.unsplash.com/400x300/?underwater,coral,glow" alt="Glow-in-the-Dark Coral Ride" class="w-full h-64 object-cover rounded-lg shadow-md">
                        </div>
                        <p class="text-red-500 horror-font leading-relaxed mt-3">
                            <span class="font-semibold text-red-300">Descend into the depths</span> of a phosphorescent underwater realm on the Glow-in-the-Dark Coral Ride. Navigate through eerie, glowing coral forests where bioluminescent creatures lurk and ancient secrets whisper from the abyss. Beware the shadows in the deep!
                        </p>
                    </div>

                    <!-- Haunted Mansion of Lost Souls -->
                    <div class="mb-6">
                        <h3 class="text-red-500 font-bold horror-font mb-2 text-center">Haunted Mansion of Lost Souls</h3>
                        <div class="relative">
                        <img src="{{ asset('images/haunted-old-mansion_909218-307.jpg') }}" alt="Haunted Mansion of Lost Souls" class="w-full h-64 object-cover rounded-lg shadow-md">
                        </div>
                        <p class="text-red-500 horror-font leading-relaxed mt-3">
                            <span class="font-semibold text-red-300">Enter the decaying gates</span> of the Haunted Mansion of Lost Souls. Explore creaking halls and shadowy chambers, each haunted by restless spirits and echoing with ghostly whispers. Every room holds a new chilling encounter, and escape is far from guaranteed.
                        </p>
                    </div>

                    <!-- Twisted Carnival of Nightmares -->
                    <div class="mb-6">
                        <h3 class="text-red-500 font-bold horror-font mb-2 text-center">Twisted Carnival of Nightmares</h3>
                        <div class="relative">
                            <img src="https://source.unsplash.com/400x300/?carnival,creepy,night" alt="Twisted Carnival of Nightmares" class="w-full h-64 object-cover rounded-lg shadow-md">
                        </div>
                        <p class="text-red-500 horror-font leading-relaxed mt-3">
                            <span class="font-semibold text-red-300">Step right up, if you dare,</span> to the Twisted Carnival of Nightmares! This is no ordinary carnival. Here, laughter is sinister, games have deadly stakes, and clowns are the stuff of pure terror.  Enjoy the twisted fun... if you can survive the night.
                        </p>
                    </div>

                    <!-- 4D Superhero Nightmare Experience -->
                    <div class="mb-6">
                        <h3 class="text-red-500 font-bold horror-font mb-2 text-center">4D Superhero Nightmare</h3>
                        <div class="relative">
                            <img src="https://source.unsplash.com/400x300/?superhero,villain,dark" alt="4D Superhero Nightmare Experience" class="w-full h-64 object-cover rounded-lg shadow-md">
                        </div>
                        <p class="text-red-500 horror-font leading-relaxed mt-3">
                            <span class="font-semibold text-red-300">Brace yourself for the ultimate sensory assault</span> in the 4D Superhero Nightmare Experience. Immerse yourself in a world where superheroes battle not just villains, but their own inner demons. Feel the tremors, smell the fear, and witness nightmares leap off the screen and into reality!
                        </p>
                    </div>

                    <!-- Add more attraction divs here following the same structure -->

                </div>
            </div>
        </div>

        <footer class="bg-black text-gray-400 p-6 mt-12">
            <div class="container mx-auto text-center">
                <p>© 2025 Horror-Bark. All rights reserved.</p>
                <p class="mt-2">
                    <a href="#" class="hover:text-red-400">Privacy Policy</a> |
                    <a href="#" class="hover:text-red-400">Terms of Service</a>
                </p>
            </div>
        </footer>
    </body>
</html>