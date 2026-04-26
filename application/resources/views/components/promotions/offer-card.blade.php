@props([
    'item',
    'promotion',
])

<article class="promotion-offer-card">
    <div class="promotion-offer-card-media">
        <img
            src="{{ $item['image'] }}"
            alt="{{ $item['title'] }}"
            class="h-full w-full object-cover grayscale-[18%] brightness-[0.72] contrast-110 transition duration-700 hover:scale-[1.03]"
            loading="lazy"
        />
    </div>

    <div class="promotion-offer-card-body">
        <div class="promotion-offer-card-heading">
            <p class="theme-kicker">{{ $item['eyebrow'] }}</p>
            <span class="catalog-range-pill">{{ $item['pricing']['discount_label'] }}</span>
        </div>

        <div class="space-y-2">
            <h2 class="promotion-offer-card-title">{{ $item['title'] }}</h2>
            <p class="readable-copy !text-[1rem]">{{ $item['subtitle'] }}</p>
        </div>

        <p class="readable-muted">{{ $item['description'] }}</p>

        <dl class="promotion-offer-meta">
            @foreach ($item['meta'] as $meta)
                <div class="promotion-offer-meta-row">
                    <dt>{{ $meta['label'] }}</dt>
                    <dd>{{ $meta['value'] }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="promotion-offer-price">
            <div>
                <p class="theme-label">{{ $item['pricing']['unit_label'] }}</p>
                <div class="mt-2 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                    <span class="readable-muted line-through decoration-primary-light/40">
                        MVR {{ number_format($item['pricing']['base_price'], 2) }}
                    </span>
                    <span class="promotion-offer-price-value">MVR {{ number_format($item['pricing']['discounted_price'], 2) }}</span>
                </div>

            </div>

            <div class="promotion-offer-savings">
                <p class="theme-label">Savings</p>
                <p>MVR {{ number_format($item['pricing']['savings'], 2) }}</p>
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
</article>
