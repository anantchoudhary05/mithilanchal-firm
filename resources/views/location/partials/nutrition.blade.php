@php($items = $section->items())
<section class="city-nutrition">
    <div class="container">
        <header class="city-section-head">
            @if($section->filled('eyebrow'))
                <p class="city-eyebrow">{{ $section->value('eyebrow') }}</p>
            @endif
            @if($section->filled('title'))
                <h2>{!! $city->highlightedHeading($section->value('title'), $section->value('title_highlight')) !!}</h2>
            @endif
            @if($section->filled('description'))
                <p>{{ $section->value('description') }}</p>
            @endif
        </header>
        @if($items !== [])
            <div class="city-nutrition-grid">
                @foreach($items as $item)
                    <article class="city-nutrition-card">
                        <span class="city-icon city-icon--soft" aria-hidden="true">
                            <i class="fa-solid {{ $section->iconClass($item['icon'] ?? 'leaf') }}"></i>
                        </span>
                        <div>
                            @if(filled($item['title'] ?? null))
                                <h3>{{ $item['title'] }}</h3>
                            @endif
                            @if(filled($item['description'] ?? null))
                                <p>{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
