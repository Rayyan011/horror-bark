@extends('layouts.app')

@section('title', 'Contact Us - Horror-Bark Theme Park')

@section('content')
    <div class="max-w-3xl mx-auto py-8">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-bold horror-font mb-4">Send us an Enquiry</h1>
            <p class="text-gray-300">We'd love to hear from you! Fill out the form below and we'll get back to you soon.</p>
        </header>

        <x-ui.alert-stack />

        <x-contact.enquiry-form :action="route('contacts.store')" />
    </div>
@endsection
