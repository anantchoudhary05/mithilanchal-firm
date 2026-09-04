@props([
    'section',
    'active' => false,
    'deferred' => false,
])

@php
    /** @var \App\Models\HomepageSection $section */
@endphp

<article
    class="hero-slide hero-slide--offer{{ $active ? ' is-active' : '' }}"
    data-slide-type="offer"
    data-offer-id="{{ $section->id }}"
    @if($deferred) data-deferred-offer="true" hidden @endif
>
    <x-offer-card :section="$section" variant="slide" />
</article>
