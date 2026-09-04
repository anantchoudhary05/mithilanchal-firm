@php
    $title = $section->value('title');
    $image = $section->imageUrl('image');
    $badges = collect($section->value('badges', []))->filter(fn ($badge) => is_array($badge) && filled($badge['title'] ?? null));
    $primaryUrl = \App\Models\CityPageSection::resolveLink($section->value('primary_cta_url'));
    $secondaryUrl = \App\Models\CityPageSection::resolveLink($section->value('secondary_cta_url'));
@endphp
<header class="city-hero">
    <div class="container city-hero-grid">
        <div class="city-hero-copy">
            @if($section->filled('tag'))
                <p class="city-hero-tag">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    {{ $section->value('tag') }}
                </p>
            @endif

            @if(filled($title))
                <h1>{{ $title }}</h1>
            @else
                <h1>Premium {{ $city->city_name }} Makhana Supplier</h1>
            @endif

            @if($section->filled('description'))
                <p class="city-hero-lead">{{ $section->value('description') }}</p>
            @endif

            <div class="city-hero-actions">
                @if($primaryUrl && $section->filled('primary_cta_text'))
                    <a class="city-btn city-btn--gold" href="{{ $primaryUrl }}">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        {{ $section->value('primary_cta_text') }}
                    </a>
                @endif
                @if($secondaryUrl && $section->filled('secondary_cta_text'))
                    <a class="city-btn city-btn--ghost" href="{{ $secondaryUrl }}">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        {{ $section->value('secondary_cta_text') }}
                    </a>
                @endif
            </div>
        </div>

        @if($image && $city->template !== \App\Support\CityPageBlueprint::TEMPLATE_MINIMAL)
            <div class="city-hero-visual">
                <div class="city-hero-ring" aria-hidden="true"></div>
                <img src="{{ $image }}" alt="{{ $section->value('image_alt') ?: $title }}" width="520" height="520">
                @foreach($badges as $index => $badge)
                    <aside class="city-hero-badge city-hero-badge--{{ $index === 0 ? 'top' : 'bottom' }}">
                        <span class="city-icon" aria-hidden="true"><i class="fa-solid {{ $section->iconClass($badge['icon'] ?? 'star') }}"></i></span>
                        <div>
                            <strong>{{ $badge['title'] }}</strong>
                            @if(filled($badge['subtext'] ?? null))
                                <span>{{ $badge['subtext'] }}</span>
                            @endif
                        </div>
                    </aside>
                @endforeach
            </div>
        @endif
    </div>
</header>
