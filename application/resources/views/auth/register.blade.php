@extends('layouts.app')

@section('title', 'Register - Horror-Bark')

@section('content')
<main class="max-w-lg mx-auto bg-gray-800 p-6 rounded shadow border border-gray-700">
    <h1 class="text-3xl font-bold mb-6 horror-font text-center">Create Account</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-300 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1" for="name">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>

        <div>
            <label class="block text-sm mb-1" for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>

        <div>
            <label class="block text-sm mb-1" for="password">Password</label>
            <input id="password" name="password" type="password" required
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>

        <div>
            <label class="block text-sm mb-1" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>

        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
            Create account
        </button>
    </form>

    <p class="mt-4 text-sm text-center text-gray-300">
        Already have an account? <a href="{{ route('login') }}" class="text-red-400 hover:text-red-300">Log in</a>
    </p>
</main>
@endsection
