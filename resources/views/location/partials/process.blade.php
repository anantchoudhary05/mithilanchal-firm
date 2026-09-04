@php($items = $section->items())
<section class="city-process">
    <div class="container">
        <header class="city-section-head city-section-head--light">
            @if($section->filled('eyebrow'))
                <p class="city-eyebrow city-eyebrow--gold">{{ $section->value('eyebrow') }}</p>
            @endif
            @if($section->filled('title'))
                <h2>{!! $city->highlightedHeading($section->value('title'), $section->value('title_highlight'), 'city-highlight-gold') !!}</h2>
            @endif
            @if($section->filled('description'))
                <p>{{ $section->value('description') }}</p>
            @endif
        </header>
        @if($items !== [])
            <ol class="city-process-grid">
                @foreach($items as $item)
                    <li class="city-process-step">
                        <span class="city-step-num">{{ $item['number'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="city-icon city-icon--muted" aria-hidden="true">
                            <i class="fa-solid {{ $section->iconClass($item['icon'] ?? 'check') }}"></i>
                        </span>
                        @if(filled($item['title'] ?? null))
                            <h3>{{ $item['title'] }}</h3>
                        @endif
                        @if(filled($item['description'] ?? null))
                            <p>{{ $item['description'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</section>
