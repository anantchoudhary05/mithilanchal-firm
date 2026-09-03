@php
    $hour = (int) now()->format('G');
    $hello = match (true) {
        $hour < 12 => 'Good morning',
        $hour < 17 => 'Good afternoon',
        default => 'Good evening',
    };
@endphp

<section class="cms-welcome">
    <div>
        <p class="cms-welcome__eyebrow">{{ now()->format('l, d F Y') }}</p>
        <h2 class="cms-welcome__title">{{ $hello }}, {{ $name }}</h2>
        <p class="cms-welcome__copy">{{ $subtitle }}</p>
    </div>
    @if (! empty($actions))
        <div class="cms-welcome__actions">
            @foreach ($actions as $action)
                <a class="lead-chip {{ $action['class'] ?? 'lead-chip--call' }}" href="{{ $action['url'] }}">
                    {{ $action['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</section>
