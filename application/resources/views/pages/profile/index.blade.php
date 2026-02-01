@extends('layouts.app')

@section('title', 'My Profile - Horror-Bark')

@section('content')
<main class="max-w-2xl mx-auto my-8 px-4 space-y-8">
    <h1 class="text-4xl font-bold horror-font">My Profile</h1>

    @if (session('status'))
        <div class="text-green-300 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="text-red-300 text-sm">{{ $errors->first() }}</div>
    @endif

    <section class="bg-gray-800 p-6 rounded border border-gray-700">
        <h2 class="text-2xl font-semibold mb-4">Account Details</h2>
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm mb-1" for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
            <div>
                <label class="block text-sm mb-1" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
            <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                Save changes
            </button>
        </form>
    </section>

    <section class="bg-gray-800 p-6 rounded border border-gray-700">
        <h2 class="text-2xl font-semibold mb-4">Change Password</h2>
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm mb-1" for="current_password">Current Password</label>
                <input id="current_password" name="current_password" type="password" required
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
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
            <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                Update password
            </button>
        </form>
    </section>
</main>
@endsection
