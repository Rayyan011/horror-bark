@extends('layouts.app')

@section('title', 'Reset Password - Horror-Bark')

@section('content')
    <x-auth.form-card
        title="Reset Password"
        :action="route('password.email')"
        method="POST"
        :fields="[
            ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'value' => old('email'), 'required' => true],
        ]"
        submit-label="Send password reset link"
        :links="[
            ['prefix' => 'Remembered your password?', 'label' => 'Log in', 'href' => route('login')],
        ]"
    >
        <x-slot:beforeFields>
            <x-ui.alert-stack />
        </x-slot:beforeFields>
    </x-auth.form-card>
@endsection
