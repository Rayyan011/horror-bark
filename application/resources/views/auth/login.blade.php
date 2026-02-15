@extends('layouts.app')

@section('title', 'Login - Horror-Bark')

@section('content')
    <x-auth.form-card
        title="Customer Login"
        :action="route('login.store')"
        method="POST"
        :fields="[
            ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'value' => old('email'), 'required' => true],
            ['label' => 'Password', 'name' => 'password', 'type' => 'password', 'required' => true],
        ]"
        submit-label="Log in"
        :links="[
            ['prefix' => 'New here?', 'label' => 'Create an account', 'href' => route('register')],
        ]"
    >
        <x-slot:beforeFields>
            <x-ui.alert-stack />
        </x-slot:beforeFields>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center text-sm">
                <input type="checkbox" name="remember" value="1" class="mr-2 rounded bg-gray-900 border-gray-700" @checked(old('remember')) />
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-red-400 hover:text-red-300">Forgot password?</a>
        </div>
    </x-auth.form-card>
@endsection
