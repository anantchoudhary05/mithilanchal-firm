@if (! empty($isPreview))
    <div class="blog-preview-banner" role="status">
        CMS preview — visitors cannot see this page unless it is published.
    </div>
@endif

@php($hasHero = $city->enabledOrderedSections()->contains(fn ($section) => $section->section_type === \App\Support\CityPageBlueprint::HERO))
@unless($hasHero)
    <header class="city-hero city-hero--plain">
        <div class="container">
            <h1>Premium {{ $city->city_name }} Makhana Supplier</h1>
        </div>
    </header>
@endunless

<script type="application/ld+json">@json($city->jsonLd())</script>

@foreach($city->enabledOrderedSections() as $section)
    @includeIf('location.partials.'.$section->section_type, [
        'city' => $city,
        'section' => $section,
    ])
@endforeach

