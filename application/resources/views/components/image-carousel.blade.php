@props(['images', 'title'])

<x-ui.media-gallery :images="$images" :alt="$title" mode="carousel" />
