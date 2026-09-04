@php($items = $section->items())
@if($items !== [] || $section->filled('title'))
    <section class="city-highlights">
        <div class="container">
            @if($section->filled('title'))
                <h2 class="city-highlights-title">{{ $section->value('title') }}</h2>
            @endif
            <div class="city-highlight-list">
                @foreach($items as $item)
                    <article class="city-highlight-row">
                        <span class="city-icon city-icon--solid" aria-hidden="true">
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
        </div>
    </section>
@endif
