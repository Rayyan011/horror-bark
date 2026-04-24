@props([
    'title' => 'Featured Discounts',
    'subtitle' => 'Selected discounted experiences from the current Horror-Bark registry.',
])

<section class="py-12">
    <x-ui.section-heading
        :title="$title"
        :subtitle="$subtitle"
        size="lg"
        align="center"
        class="mb-14"
    />

    <div class="grid grid-cols-1 gap-8 md:grid-cols-3 md:[&>*:nth-child(2)]:-translate-y-8">
        {{ $slot }}
    </div>
</section>
