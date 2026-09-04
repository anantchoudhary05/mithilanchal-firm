@php
    $primaryUrl = \App\Models\CityPageSection::resolveLink($section->value('primary_cta_url'));
    $secondaryUrl = \App\Models\CityPageSection::resolveLink($section->value('secondary_cta_url'));
@endphp
<aside class="city-location-bar">
    <div class="container city-location-bar-inner">
        <div>
            @if($section->filled('title'))
                <p class="city-location-bar-title">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    {{ $section->value('title') }}
                </p>
            @endif
            @if($section->filled('description'))
                <p>{{ $section->value('description') }}</p>
            @endif
        </div>
        <div class="city-location-bar-actions">
            @if($primaryUrl && $section->filled('primary_cta_text'))
                <a class="city-btn city-btn--green" href="{{ $primaryUrl }}">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    {{ $section->value('primary_cta_text') }}
                </a>
            @endif
            @if($secondaryUrl && $section->filled('secondary_cta_text'))
                <a class="city-btn city-btn--outline-gold" href="{{ $secondaryUrl }}">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                    {{ $section->value('secondary_cta_text') }}
                </a>
            @endif
        </div>
    </div>
</aside>
