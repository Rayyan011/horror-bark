<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - Horror-Bark Theme Park</title>
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
                    <li><a href="{{ route('contact') }}" class="hover:text-gray-400">Contact</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-gray-400">About Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-red-500 horror-font mb-6 text-center">Contact Horror-Bark</h1>

        <div class="bg-gray-800 shadow-md rounded-lg p-8">
            <p class="text-gray-300 leading-relaxed mb-6 text-center">
                Dare to reach out to the spectral staff of Horror-Bark?  For inquiries, bookings, or to share your chilling tales, contact us below!
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-red-400 horror-font mb-3">General Inquiries (Spectral Dispatch)</h2>
                    <p class="text-gray-300 leading-relaxed mb-2">
                        Email: <a href="mailto:whispers@horror-bark.com" class="text-red-300 hover:underline">whispers@horror-bark.com</a>  <!-- Horror-Bark Themed Email -->
                    </p>
                    <p class="text-gray-300 leading-relaxed mb-2">
                        Phone (Maldives): +960 777 6666   <!-- Plausible Maldivian Number -->
                    </p>
                    <p class="text-gray-300 leading-relaxed">
                        Our phantom operators are standing by... or perhaps floating. Response times may vary depending on ectoplasmic interference.
                    </p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-red-400 horror-font mb-3">Headquarters (Bark Federation Pvt Ltd)</h2> <!-- Company Name -->
                    <p class="text-gray-300 leading-relaxed mb-2">
                        Bark Federation Pvt Ltd<br> <!-- Company Name -->
                        Shadow Isle Corporate Plaza, 7th Floor<br>      <!-- Thematic Street Address -->
                        Orchid Magu, Malé, 20002<br> <!-- Male City, Plausible Zip Code -->
                        Maldives                 <!-- Country -->
                    </p>
                    <p class="text-gray-300 leading-relaxed">
                        Please note: This address is for our earthly headquarters.  Brave visitors seeking the *true* Horror-Bark experience must travel to the island itself (ferry access required).
                    </p>
                </div>
            </div>

            <div class="mt-8 text-center">
                <p class="text-gray-300">
                    For truly urgent matters, try sending a raven.  Spectral mail is under development.
                </p>
                <!-- You can add a contact form here later -->
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