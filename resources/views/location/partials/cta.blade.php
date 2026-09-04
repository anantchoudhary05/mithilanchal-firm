@php
    $primaryUrl = \App\Models\CityPageSection::resolveLink($section->value('primary_cta_url'));
    $secondaryUrl = \App\Models\CityPageSection::resolveLink($section->value('secondary_cta_url'));
    $whatsappUrl = \App\Models\CityPageSection::resolveLink($section->value('whatsapp_url'));
    $bg = $section->imageUrl('background_image');
@endphp
<section class="city-cta" @if($bg) style="--city-cta-image: url('{{ $bg }}')" @endif>
    <div class="container city-cta-inner">
        @if($section->filled('eyebrow'))
            <p class="city-eyebrow city-eyebrow--gold">{{ $section->value('eyebrow') }}</p>
        @endif
        @if($section->filled('title'))
            <h2>{{ $section->value('title') }}</h2>
        @endif
        @if($section->filled('description'))
            <p>{{ $section->value('description') }}</p>
        @endif
        <div class="city-hero-actions city-cta-actions">
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
            @if($whatsappUrl && $section->filled('whatsapp_text'))
                <a class="city-btn city-btn--wa" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                    {{ $section->value('whatsapp_text') }}
                </a>
            @endif
        </div>
    </div>
</section>
