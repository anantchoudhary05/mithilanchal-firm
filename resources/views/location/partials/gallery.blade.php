@php($items = $section->items())
@if($items !== [])
    <section class="city-gallery">
        <div class="container">
            <header class="city-section-head">
                @if($section->filled('eyebrow'))
                    <p class="city-eyebrow">{{ $section->value('eyebrow') }}</p>
                @endif
                @if($section->filled('title'))
                    <h2>{{ $section->value('title') }}</h2>
                @endif
            </header>
            <div class="city-gallery-grid">
                @foreach($items as $item)
                    @php($src = \App\Models\CityPageSection::resolvePublicUrl($item['image'] ?? null))
                    @continue(! $src)
                    <figure>
                        <img src="{{ $src }}" alt="{{ $item['alt'] ?? $item['caption'] ?? $city->city_name.' makhana' }}" loading="lazy" width="420" height="320">
                        @if(filled($item['caption'] ?? null))
                            <figcaption>{{ $item['caption'] }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
@endif
