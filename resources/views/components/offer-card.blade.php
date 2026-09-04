@props([
    'section',
    'variant' => 'slide',
])

@php
    /** @var \App\Models\HomepageSection $section */
    $closable = $variant === 'popup';
    $ctaUrl = $section->primaryButtonUrl() ?: route('Product');
    $productImage = $section->productImageUrl();
    $bowlImage = $section->bowlImageUrl();
    $features = $section->featureItems();
    $sealWords = filled($section->badge_text)
        ? preg_split('/\s+/', trim((string) $section->badge_text))
        : [];
@endphp

<article class="offer-card offer-card--{{ $variant }}">
    @if($closable)
        <button class="offer-card__close" type="button" data-offer-close aria-label="Close welcome offer">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif

    <div class="offer-card__body">
        <div class="offer-card__copy">
            @if(filled($section->tagline))
                <p class="offer-card__tagline">
                    {{ $section->tagline }}
                    <span class="offer-card__heart" aria-hidden="true">♡</span>
                </p>
            @endif

            @if(filled($section->headline))
                <h2 class="offer-card__headline" @if($closable) id="offer-popup-title" @endif>{{ $section->headline }}</h2>
            @endif

            @if(filled($section->discount_text) || filled($section->discount_subtext))
                <div class="offer-card__ticket">
                    @if(filled($section->discount_text))
                        <strong>{{ $section->discount_text }}</strong>
                    @endif
                    @if(filled($section->discount_subtext))
                        <span>{{ $section->discount_subtext }}</span>
                    @endif
                </div>
            @endif

            @if(filled($section->coupon_code))
                <p class="offer-card__code">
                    <span>Use Code:</span>
                    <em>{{ $section->coupon_code }}</em>
                </p>
            @endif

            @if($features !== [])
                <ul class="offer-card__features">
                    @foreach($features as $feature)
                        <li>
                            <span class="offer-card__feature-icon" aria-hidden="true">
                                <i class="fa-solid {{ $feature['icon_class'] }}"></i>
                            </span>
                            <span>{{ $feature['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="offer-card__visual">
            @if($sealWords !== [])
                <div class="offer-card__seal" aria-hidden="true">
                    <span class="offer-card__seal-ring">
                        @foreach($sealWords as $word)
                            <small>{{ $word }}</small>
                        @endforeach
                    </span>
                    <i class="fa-solid fa-heart"></i>
                </div>
            @endif

            @if($productImage)
                <img
                    class="offer-card__pack"
                    src="{{ $productImage }}"
                    alt="{{ $section->headline ?: 'Makhana offer' }}"
                    loading="{{ $variant === 'popup' ? 'eager' : 'lazy' }}"
                >
            @endif

            @if($bowlImage)
                <img
                    class="offer-card__bowl"
                    src="{{ $bowlImage }}"
                    alt=""
                    loading="lazy"
                >
            @endif
        </div>
    </div>

    <div class="offer-card__footer">
        @if(filled($section->shipping_text))
            <div class="offer-card__shipping">
                <span class="offer-card__footer-icon" aria-hidden="true">
                    <i class="fa-solid fa-truck"></i>
                </span>
                <p>{{ $section->shipping_text }}</p>
            </div>
        @endif

        <div class="offer-card__cta-wrap">
            @if(filled($section->button_text))
                <a href="{{ $ctaUrl }}" class="offer-card__cta">
                    {{ $section->button_text }}
                    <span aria-hidden="true">›</span>
                </a>
            @endif
            @if(filled($section->urgency_text))
                <p class="offer-card__urgency">{{ $section->urgency_text }}</p>
            @endif
        </div>

        @if(filled($section->social_proof))
            <div class="offer-card__proof">
                <span class="offer-card__footer-icon offer-card__footer-icon--badge" aria-hidden="true">
                    <i class="fa-solid fa-certificate"></i>
                </span>
                <div>
                    <p>{{ $section->social_proof }}</p>
                    <span class="offer-card__stars" aria-hidden="true">★★★★★</span>
                </div>
            </div>
        @endif
    </div>
</article>
