<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - Horror-Bark Theme Park</title>
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
                    <li><a href="#" class="hover:text-gray-400">Theme Park</a></li>
                    <li><a href="#" class="hover:text-gray-400">Beach Events</a></li>
                    <li><a href="#" class="hover:text-gray-400">Contact</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-gray-400">About Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-red-500 horror-font mb-6 text-center">About Horror-Bark Theme Park</h1>

        <div class="bg-gray-800 shadow-md rounded-lg p-8">
            <p class="text-gray-300 leading-relaxed mb-4">
                Welcome to Horror-Bark, the ultimate destination for thrill-seekers and horror enthusiasts! Nestled on a remote, mist-shrouded island, far from the mundane world, our theme park offers a uniquely terrifying blend of attractions, eerie events, and truly spine-chilling experiences. Prepare to be immersed in a realm where nightmares come alive, and every shadow holds a secret.
            </p>
            <p class="text-gray-300 leading-relaxed mb-4">
                From our haunted hotels, rumored to be occupied by restless spirits, and ghostly beach events under the pale moonlight, to the terrifying Theme Park rides that will test your courage and sanity, Horror-Bark is meticulously designed to push your limits and immerse you in a world of delightful dread.  Each corner of our island whispers tales of the macabre and unknown.
            </p>
            <p class="text-gray-300 leading-relaxed mb-4">
                Our mission is to create unforgettable memories (if you dare!) and provide an escape into the thrilling unknown for those brave enough to seek it. We strive to offer an experience that lingers in your mind long after you've left our shores, a chilling reminder of the darkness and excitement that awaits at Horror-Bark. Book your stay in one of our haunted hotels, secure your ferry tickets for the journey across treacherous waters, and prepare for an adventure unlike any other!
            </p>
            <h2 class="text-2xl font-bold text-red-400 horror-font mt-6 mb-3">Our Featured Experiences Include:</h2>
            <ul class="list-disc list-inside text-gray-300 mb-4">
                <li><span class="font-semibold text-red-300">Horror-Bark Theme Park:</span> Featuring pulse-pounding rides like the Space Exploration Roller Coaster, plunging you into the cosmic abyss, and the Glow-in-the-Dark Coral Ride, navigating through phosphorescent underwater terrors.</li>
                <li><span class="font-semibold text-red-300">Eerie Beach Events:</span> Join us for ghostly jet skiing across the haunted bay, moonlit paddleboarding under the watchful eyes of island specters, and mysterious concerts echoing with otherworldly melodies.</li>
                <li><span class="font-semibold text-red-300">Island Hotels:</span> Book your stay in accommodations where every creak and shadow tells a story. Unlock exclusive spine-chilling experiences only available to our overnight guests.</li>
                <li><span class="font-semibold text-red-300">Additional Attractions:</span> Dare to brave the Superhero 4D Nightmare Experience, where villains break free from the screen, and get lost in the terrifying twists and turns of the Haunted Maze, where reality blurs with illusion.</li>
                <li>... and many more unspeakable horrors and delightful frights to discover!</li>
            </ul>
            <p class="text-gray-300 leading-relaxed">
                Are you brave enough to explore Horror-Bark? We eagerly await your arrival... if you survive the ferry ride!  Mwahahaha!
            </p>
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