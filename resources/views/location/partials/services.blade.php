@php($items = $section->items())
@if($items !== [] || $section->filled('title'))
    <section class="city-services">
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
            <div class="city-card-grid">
                @foreach($items as $item)
                    @php($url = \App\Models\CityPageSection::resolveLink($item['url'] ?? null))
                    <article class="city-service-card">
                        <span class="city-icon city-icon--solid" aria-hidden="true">
                            <i class="fa-solid {{ $section->iconClass($item['icon'] ?? 'bowl-food') }}"></i>
                        </span>
                        @if(filled($item['title'] ?? null))
                            <h3>{{ $item['title'] }}</h3>
                        @endif
                        @if(filled($item['description'] ?? null))
                            <p>{{ $item['description'] }}</p>
                        @endif
                        @if($url)
                            <a href="{{ $url }}">View offering</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
