@extends('layouts.app')

@section('title', 'Set New Password - Horror-Bark')

@section('content')
<main class="max-w-lg mx-auto bg-gray-800 p-6 rounded shadow border border-gray-700">
    <h1 class="text-3xl font-bold mb-6 horror-font text-center">Set New Password</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-300 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}" />
        <input type="hidden" name="email" value="{{ $email }}" />

        <div>
            <label class="block text-sm mb-1" for="password">New Password</label>
            <input id="password" name="password" type="password" required
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>

        <div>
            <label class="block text-sm mb-1" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>

        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
            Update password
        </button>
    </form>
</main>
@endsection
