@php($items = $section->items())
@if($items !== [] || $section->filled('title'))
    <section class="city-places">
        <div class="container">
            <header class="city-section-head">
                @if($section->filled('eyebrow'))
                    <p class="city-eyebrow">{{ $section->value('eyebrow') }}</p>
                @endif
                @if($section->filled('title'))
                    <h2>{{ $section->value('title') }}</h2>
                @endif
                @if($section->filled('description'))
                    <p>{{ $section->value('description') }}</p>
                @endif
            </header>
            <div class="city-card-grid city-card-grid--places">
                @foreach($items as $item)
                    @php($url = \App\Models\CityPageSection::resolveLink($item['url'] ?? null))
                    <article class="city-place-card">
                        @if(filled($item['image'] ?? null))
                            <img src="{{ \App\Models\CityPageSection::resolvePublicUrl($item['image']) }}" alt="{{ $item['image_alt'] ?? $item['name'] ?? '' }}" loading="lazy" width="400" height="240">
                        @endif
                        <div class="city-place-body">
                            @if(filled($item['name'] ?? null))
                                <h3>{{ $item['name'] }}</h3>
                            @endif
                            @if(filled($item['location'] ?? null))
                                <p class="city-place-meta"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $item['location'] }}</p>
                            @endif
                            @if(filled($item['description'] ?? null))
                                <p>{{ $item['description'] }}</p>
                            @endif
                            @if($url)
                                <a href="{{ $url }}">Learn more</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
