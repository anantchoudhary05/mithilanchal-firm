@php($items = $section->items())
@if($items !== [])
    <section class="city-reviews">
        <div class="container">
            <header class="city-section-head">
                @if($section->filled('eyebrow'))
                    <p class="city-eyebrow">{{ $section->value('eyebrow') }}</p>
                @endif
                @if($section->filled('title'))
                    <h2>{!! $city->highlightedHeading($section->value('title'), $section->value('title_highlight')) !!}</h2>
                @endif
            </header>
            <div class="city-review-grid">
                @foreach($items as $item)
                    <blockquote class="city-review-card">
                        @php($rating = max(1, min(5, (int) ($item['rating'] ?? 5))))
                        <p class="city-stars" aria-label="{{ $rating }} out of 5 stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $rating ? 'solid' : 'regular' }} fa-star" aria-hidden="true"></i>
                            @endfor
                        </p>
                        @if(filled($item['review'] ?? null))
                            <p class="city-review-text">“{{ $item['review'] }}”</p>
                        @endif
                        <footer class="city-review-author">
                            @if(filled($item['image'] ?? null))
                                <img src="{{ \App\Models\CityPageSection::resolvePublicUrl($item['image']) }}" alt="" loading="lazy" width="44" height="44">
                            @else
                                <span class="city-avatar" aria-hidden="true">{{ strtoupper(substr((string) ($item['name'] ?? 'B'), 0, 1)) }}</span>
                            @endif
                            <cite>
                                <strong>{{ $item['name'] ?? 'Buyer' }}</strong>
                                @if(filled($item['designation'] ?? null))
                                    <span>{{ $item['designation'] }}</span>
                                @endif
                            </cite>
                        </footer>
                    </blockquote>
                @endforeach
            </div>
        </div>
    </section>
@endif
