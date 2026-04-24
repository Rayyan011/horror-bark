@props([
    'item',
    'promotion',
])

<x-ui.surface class="overflow-hidden p-0">
    <div class="aspect-[5/4] overflow-hidden border-b border-primary-light/10 bg-black">
        <img
            src="{{ $item['image'] }}"
            alt="{{ $item['title'] }}"
            class="h-full w-full object-cover grayscale-[18%] brightness-[0.72] contrast-110 transition duration-1000 hover:scale-105"
            loading="lazy"
        />
    </div>

    <div class="space-y-5 p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="space-y-2">
                <p class="theme-kicker">{{ $item['eyebrow'] }}</p>
                <h2 class="catalog-card-title !text-[1.7rem]">{{ $item['title'] }}</h2>
                <p class="readable-copy !text-[1.02rem]">{{ $item['subtitle'] }}</p>
            </div>

            <span class="catalog-range-pill">{{ $item['pricing']['discount_label'] }}</span>
        </div>

        <p class="readable-muted">{{ $item['description'] }}</p>

        <div class="grid gap-3 sm:grid-cols-3">
            @foreach ($item['meta'] as $meta)
                <div class="theme-detail-card">
                    <p class="theme-label">{{ $meta['label'] }}</p>
                    <p class="theme-detail-value">{{ $meta['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-3 md:grid-cols-[1fr,1fr,auto]">
            <div class="theme-detail-card">
                <p class="theme-label">{{ $item['pricing']['unit_label'] }}</p>
                <p class="readable-muted line-through decoration-primary-light/40">
                    MVR {{ number_format($item['pricing']['base_price'], 2) }}
                </p>
                <p class="theme-detail-value">MVR {{ number_format($item['pricing']['discounted_price'], 2) }}</p>
            </div>

            <div class="theme-detail-card">
                <p class="theme-label">Savings</p>
                <p class="theme-detail-value">MVR {{ number_format($item['pricing']['savings'], 2) }}</p>
                <p class="readable-muted">{{ $promotion->resolved_title }}</p>
            </div>

            <div class="theme-detail-card flex items-center justify-center text-center">
                <div>
                    <p class="theme-label">Claimed In</p>
                    <p class="theme-detail-value">Demo Checkout</p>
                </div>
            </div>
        </div>

        @auth
            <x-booking.form
                :action="$item['form']['action']"
                :mode="$item['form']['mode']"
                :rules-hint="$item['form']['rulesHint']"
                :submit-label="$item['form']['submitLabel']"
                :submit-variant="$item['form']['submitVariant'] ?? 'primary'"
                :quantity-config="$item['form']['quantityConfig']"
                :id-prefix="$item['form']['idPrefix']"
                :hidden="$item['form']['hidden'] ?? []"
                :values="$item['form']['values'] ?? []"
            />
        @else
            <x-ui.auth-gate-cta
                :login-href="route('login')"
                :label="'Log in to claim '.$promotion->resolved_title"
            />
        @endauth
    </div>
</x-ui.surface>
