@php($items = $section->items())
<section class="city-faq" id="city-faq">
    <div class="container city-faq-grid">
        <header class="city-faq-intro">
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
            <div class="city-faq-list">
                @foreach($items as $item)
                    @continue(! filled($item['question'] ?? null))
                    <details class="city-faq-item" {{ $loop->first ? 'open' : '' }}>
                        <summary>{{ $item['question'] }}</summary>
                        @if(filled($item['answer'] ?? null))
                            <p>{{ $item['answer'] }}</p>
                        @endif
                    </details>
                @endforeach
            </div>
        @endif
    </div>
</section>
