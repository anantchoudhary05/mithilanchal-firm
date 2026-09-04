@php($items = $section->items())
@if($items !== [])
    <section class="city-stats" aria-label="{{ $city->city_name }} highlights">
        <div class="container city-stats-grid">
            @foreach($items as $item)
                <div class="city-stat">
                    <p class="city-stat-value">{{ $item['value'] ?? $item['title'] ?? '' }}</p>
                    <p class="city-stat-label">{{ $item['label'] ?? $item['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endif
