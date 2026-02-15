@extends('layouts.app')

@section('title', 'Set New Password - Horror-Bark')

@section('content')
    <x-auth.form-card
        title="Set New Password"
        :action="route('password.update')"
        method="POST"
        :fields="[
            ['type' => 'hidden', 'name' => 'token', 'value' => $token],
            ['type' => 'hidden', 'name' => 'email', 'value' => $email],
            ['label' => 'New Password', 'name' => 'password', 'type' => 'password', 'required' => true],
            ['label' => 'Confirm Password', 'name' => 'password_confirmation', 'type' => 'password', 'required' => true],
        ]"
        submit-label="Update password"
    >
        <x-slot:beforeFields>
            <x-ui.alert-stack />
        </x-slot:beforeFields>
    </x-auth.form-card>
@endsection
