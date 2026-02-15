@props([
    'title' => 'Featured Experiences',
])

<section>
    <x-ui.section-heading :title="$title" size="md" class="mb-4" />
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{ $slot }}
    </div>
</section>
