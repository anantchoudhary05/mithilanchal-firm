@php($items = $section->items())
<section class="city-why">
    <div class="container">
        <header class="city-section-head">
            @if($section->filled('eyebrow'))
                <p class="city-eyebrow city-eyebrow--gold">{{ $section->value('eyebrow') }}</p>
            @endif
            @if($section->filled('title'))
                <h2>{{ $section->value('title') }}</h2>
            @endif
            @if($section->filled('description'))
                <p>{{ $section->value('description') }}</p>
            @endif
        </header>
        @if($items !== [])
            <div class="city-why-grid">
                @foreach($items as $item)
                    <article class="city-why-card {{ ! empty($item['featured']) ? 'is-featured' : '' }}">
                        <span class="city-icon {{ ! empty($item['featured']) ? 'city-icon--gold' : 'city-icon--solid' }}" aria-hidden="true">
                            <i class="fa-solid {{ $section->iconClass($item['icon'] ?? 'leaf') }}"></i>
                        </span>
                        @if(filled($item['title'] ?? null))
                            <h3>{{ $item['title'] }}</h3>
                        @endif
                        @if(filled($item['description'] ?? null))
                            <p>{{ $item['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
