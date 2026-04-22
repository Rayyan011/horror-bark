@props([
    'members' => [],
])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach ($members as $member)
        <x-ui.surface padding="p-0" class="overflow-hidden h-full">
            <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}" class="h-80 w-full object-cover">

            <div class="space-y-3 p-5">
                <p class="theme-kicker">{{ $member['role'] }}</p>
                <h3 class="text-2xl font-semibold text-moonlight horror-font">{{ $member['name'] }}</h3>

                @if (!empty($member['focus']))
                    <div class="theme-detail-card !px-3 !py-3">
                        <p class="theme-label">Keeps Watch Over</p>
                        <p class="theme-detail-value !mt-1 !text-sm">{{ $member['focus'] }}</p>
                    </div>
                @endif

                @if (!empty($member['bio']))
                    <p class="readable-muted">{{ $member['bio'] }}</p>
                @endif
            </div>
        </x-ui.surface>
    @endforeach
</div>
