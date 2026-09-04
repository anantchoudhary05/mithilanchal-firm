@php
    $image = $section->imageUrl('image');
    $inset = $section->imageUrl('inset_image');
    $buttonUrl = \App\Models\CityPageSection::resolveLink($section->value('button_url'));
@endphp
<section class="city-about">
    <div class="container city-about-grid">
        @if($city->template !== \App\Support\CityPageBlueprint::TEMPLATE_MINIMAL)
            <div class="city-about-media">
                @if($image)
                    <figure class="city-about-photo">
                        <img src="{{ $image }}" alt="{{ $section->value('image_alt') ?: $section->value('title') }}" loading="lazy" width="640" height="520">
                        @if($section->filled('badge_text'))
                            <span class="city-ribbon">{{ $section->value('badge_text') }}</span>
                        @endif
                    </figure>
                @endif
                @if($inset)
                    <figure class="city-about-inset">
                        <img src="{{ $inset }}" alt="{{ $section->value('inset_image_alt') ?: 'Makhana close-up' }}" loading="lazy" width="280" height="220">
                    </figure>
                @endif
            </div>
        @endif
        <div class="city-about-copy">
            @if($section->filled('eyebrow'))
                <p class="city-eyebrow">{{ $section->value('eyebrow') }}</p>
            @endif
            @if($section->filled('title'))
                <h2>{{ $section->value('title') }}</h2>
            @endif
            @if($section->filled('description'))
                <p>{{ $section->value('description') }}</p>
            @endif
            @if($buttonUrl && $section->filled('button_text'))
                <a class="city-btn city-btn--green" href="{{ $buttonUrl }}">{{ $section->value('button_text') }}</a>
            @endif
        </div>
    </div>
</section>
