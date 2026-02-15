@extends('layouts.app')

@section('title', 'My Profile - Horror-Bark')

@section('content')
<main class="max-w-2xl mx-auto my-8 px-4 space-y-8">
    <h1 class="text-4xl font-bold horror-font">My Profile</h1>

    <x-ui.alert-stack />

    <x-profile.account-form :user="$user" :action="route('profile.update')" />
    <x-profile.password-form :action="route('profile.password')" />
</main>
@endsection
