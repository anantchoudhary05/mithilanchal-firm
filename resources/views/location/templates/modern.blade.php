<x-layout
    :meta_title="$meta_title"
    :meta_description="$meta_description"
    :meta_keywords="$meta_keywords"
    :canonical_url="$canonical_url"
    :robots="$robots"
    :og_title="$og_title"
    :og_description="$og_description"
    :og_image="$og_image"
>
    <article class="city-page city-page--modern" data-city-template="modern">
        @include('location.partials.page-body')
    </article>
</x-layout>
