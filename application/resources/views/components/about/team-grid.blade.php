@props([
    'members' => [],
])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach ($members as $member)
        <x-ui.surface class="text-center">
            <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
            <h3 class="text-xl font-semibold text-gray-300 mb-1 horror-font">{{ $member['name'] }}</h3>
            <p class="text-gray-500 text-sm">{{ $member['role'] }}</p>
        </x-ui.surface>
    @endforeach
</div>
