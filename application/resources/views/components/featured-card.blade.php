@props(['title', 'description', 'image' => null, 'images' => [], 'link' => null, 'linkText' => 'Learn More'])

<x-ui.entity-card
    :title="$title"
    :description="$description"
    :media="[
        'images' => $images,
        'fallback' => $image,
        'alt' => $title,
    ]"
    :actions="$link ? [['label' => $linkText, 'href' => $link, 'variant' => 'ghost']] : []"
/>
