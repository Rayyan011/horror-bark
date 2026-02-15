@props([
    'action',
])

<x-ui.surface>
    <h2 class="text-2xl font-semibold mb-4">Change Password</h2>

    <x-ui.form method="PATCH" :action="$action" class="space-y-4">
        <x-ui.field label="Current Password" name="current_password" type="password" required />
        <x-ui.field label="New Password" name="password" type="password" required />
        <x-ui.field label="Confirm Password" name="password_confirmation" type="password" required />

        <x-ui.button type="submit">Update password</x-ui.button>
    </x-ui.form>
</x-ui.surface>
