@props([
    'action',
])

<x-ui.surface>
    <x-ui.form :action="$action" method="POST" class="space-y-6">
        <x-ui.field label="First Name" name="first_name" type="text" :value="old('first_name')" required />
        <x-ui.field label="Last Name" name="last_name" type="text" :value="old('last_name')" required />
        <x-ui.field label="Email" name="email" type="email" :value="old('email')" required />
        <x-ui.field label="Phone Number (Optional)" name="phone_number" type="text" :value="old('phone_number')" />
        <x-ui.textarea label="Enquiry" name="message" :value="old('message')" rows="5" required />

        <div class="text-center">
            <x-ui.button type="submit">Submit Enquiry</x-ui.button>
        </div>
    </x-ui.form>
</x-ui.surface>
