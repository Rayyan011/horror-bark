@extends('layouts.app')

@section('title', 'Register - Horror-Bark')

@section('content')
    <x-auth.form-card
        title="Create Account"
        :action="route('register.store')"
        method="POST"
        :fields="[
            ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'value' => old('name'), 'required' => true],
            ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'value' => old('email'), 'required' => true],
            ['label' => 'Password', 'name' => 'password', 'type' => 'password', 'required' => true],
            ['label' => 'Confirm Password', 'name' => 'password_confirmation', 'type' => 'password', 'required' => true],
        ]"
        submit-label="Create account"
        :links="[
            ['prefix' => 'Already have an account?', 'label' => 'Log in', 'href' => route('login')],
        ]"
    >
        <x-slot:beforeFields>
            <x-ui.alert-stack />
        </x-slot:beforeFields>
    </x-auth.form-card>
@endsection
