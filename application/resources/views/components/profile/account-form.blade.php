@props([
    'user',
    'action',
])

<x-ui.surface>
    <h2 class="text-2xl font-semibold mb-4">Account Details</h2>

    <x-ui.form method="PATCH" :action="$action" class="space-y-4">
        <x-ui.field label="Name" name="name" type="text" :value="old('name', $user->name)" required />
        <x-ui.field label="Email" name="email" type="email" :value="old('email', $user->email)" required />

        <x-ui.button type="submit">Save changes</x-ui.button>
    </x-ui.form>
</x-ui.surface>
