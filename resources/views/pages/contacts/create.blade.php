@extends('layouts.app') {{-- Extend the layout --}}

@section('title', 'Contact Us - Horror-Bark Theme Park') {{-- Optional: Page-specific title --}}

@section('content') {{-- Start content section --}}
    <!-- Container -->
    <div class="container mx-auto px-4 py-16">
        <!-- Header -->
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-bold horror-font mb-4">Send us an Enquiry</h1>
            <p class="text-gray-300">We'd love to hear from you! Fill out the form below and we'll get back to you soon.</p>
        </header>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-700 text-white p-4 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-700 text-white p-4 rounded-md mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Contact Form -->
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg border border-gray-700">
            <form action="{{ route('contacts.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="first_name" class="block text-gray-300 text-sm font-bold mb-2">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline bg-gray-700 border-gray-600" required>
                </div>

                <div>
                    <label for="last_name" class="block text-gray-300 text-sm font-bold mb-2">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline bg-gray-700 border-gray-600" required>
                </div>

                <div>
                    <label for="email" class="block text-gray-300 text-sm font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline bg-gray-700 border-gray-600" required>
                </div>

                <div>
                    <label for="phone_number" class="block text-gray-300 text-sm font-bold mb-2">Phone Number (Optional):</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline bg-gray-700 border-gray-600">
                </div>

                <div>
                    <label for="message" class="block text-gray-300 text-sm font-bold mb-2">Enquiry:</label>
                    <textarea id="message" name="message" rows="5" required
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline bg-gray-700 border-gray-600">{{ old('message') }}</textarea>
                </div>

                <div class="text-center">
                    <button type="submit"
                            class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline horror-font">
                        Submit Enquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection {{-- End content section --}}