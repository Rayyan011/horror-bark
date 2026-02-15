@props([
    'title' => 'Other Attractions at Horror-Bark',
])

<section class="mt-20">
    <x-ui.section-heading :title="$title" size="md" class="mb-4" />
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{ $slot }}
    </div>
</section>
