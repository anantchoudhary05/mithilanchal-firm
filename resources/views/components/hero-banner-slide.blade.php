@props([
    'section',
    'active' => false,
])

@php
    /** @var \App\Models\HomepageSection $section */
    $background = $section->backgroundImageUrl() ?: asset('assests/img/hq-roasted.jpg');
    $primaryUrl = $section->primaryButtonUrl();
    $secondaryUrl = $section->secondaryButtonUrl();
@endphp

<article
    class="hero-slide hero-slide--banner{{ $active ? ' is-active' : '' }}"
    data-slide-type="hero_banner"
>
    <div class="hero-slide__media" style="background-image: url('{{ $background }}')"></div>
    <div class="hero-overlay"></div>

    <div class="container hero-content">
        @if(filled($section->eyebrow))
            <div class="eyebrow light">
                {{ $section->eyebrow }}
            </div>
        @endif

        @if(filled($section->headline) || filled($section->headline_highlight))
            <h1>
                {{ $section->headline }}
                @if(filled($section->headline_highlight))
                    <span>{{ $section->headline_highlight }}</span>
                @endif
            </h1>
        @endif

        @if(filled($section->description))
            <p>{{ $section->description }}</p>
        @endif

        @if((filled($section->button_text) && $primaryUrl) || (filled($section->button_2_text) && $secondaryUrl))
            <div class="hero-buttons">
                @if(filled($section->button_text) && $primaryUrl)
                    <a href="{{ $primaryUrl }}" class="btn btn-primary">
                        {{ $section->button_text }}
                    </a>
                @endif

                @if(filled($section->button_2_text) && $secondaryUrl)
                    <a href="{{ $secondaryUrl }}" class="btn btn-white">
                        {{ $section->button_2_text }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</article>
