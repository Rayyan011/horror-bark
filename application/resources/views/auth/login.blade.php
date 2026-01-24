@extends('layouts.app')

@section('title', 'Login - Horror-Bark')

@section('content')
<main class="max-w-lg mx-auto bg-gray-800 p-6 rounded shadow border border-gray-700">
    <h1 class="text-3xl font-bold mb-6 horror-font text-center">Customer Login</h1>

    @if (session('status'))
        <div class="mb-4 text-green-300 text-sm">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 text-red-300 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1" for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>

        <div>
            <label class="block text-sm mb-1" for="password">Password</label>
            <input id="password" name="password" type="password" required
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center text-sm">
                <input type="checkbox" name="remember" class="mr-2 rounded bg-gray-900 border-gray-700" />
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-red-400 hover:text-red-300">Forgot password?</a>
        </div>

        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
            Log in
        </button>
    </form>

    <p class="mt-4 text-sm text-center text-gray-300">
        New here? <a href="{{ route('register') }}" class="text-red-400 hover:text-red-300">Create an account</a>
    </p>
</main>
@endsection
