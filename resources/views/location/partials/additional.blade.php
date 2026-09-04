@if($section->filled('body') || $section->filled('title'))
    <section class="city-extra">
        <div class="container city-extra-inner">
            @if($section->filled('title'))
                <h2>{{ $section->value('title') }}</h2>
            @endif
            @if($section->filled('body'))
                <div class="city-extra-body">
                    {!! $section->value('body') !!}
                </div>
            @endif
        </div>
    </section>
@endif
