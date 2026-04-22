@props([
    'email',
    'phone',
    'location',
])

<div class="grid gap-4 md:grid-cols-3">
    <div class="theme-detail-card">
        <p class="theme-label">Email</p>
        <p class="theme-detail-value !text-sm">
            <a href="mailto:{{ $email }}" class="theme-inline-link !text-primary-light">{{ $email }}</a>
        </p>
    </div>
    <div class="theme-detail-card">
        <p class="theme-label">Phone</p>
        <p class="theme-detail-value !text-sm">{{ $phone }}</p>
    </div>
    <div class="theme-detail-card">
        <p class="theme-label">Coordinates</p>
        <p class="theme-detail-value !text-sm">{{ $location }}</p>
    </div>
</div>
