<x-layout
    :meta_title="$meta_title"
    :meta_description="$meta_description"
    :meta_keywords="$meta_keywords"
>
    <section class="blog-hero">
        <div class="container">
            <p class="blog-hero-eyebrow">Insights & Stories</p>
            <h1 class="blog-hero-title">Our Latest Stories</h1>
            <p class="blog-hero-subtitle">
                Discover healthy recipes, nutritional facts about Fox Nuts, and insights into our premium makhana processing.
            </p>
        </div>
    </section>

    <section class="blog-listing-section">
        <div class="container">
            <div class="blog-card-grid">
                @forelse ($blogs as $blog)
                    <article class="blog-card">
                        <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-link">
                            <div class="blog-card-media">
                                @if ($blog->featured_image_url)
                                    <img src="{{ $blog->featured_image_url }}" alt="{{ $blog->image_alt ?: $blog->title }}" loading="lazy" decoding="async">
                                @else
                                    <img src="{{ asset('assests/img/hq-white.jpg') }}" alt="{{ $blog->image_alt ?: $blog->title }}" loading="lazy" decoding="async">
                                @endif
                                <span class="blog-date-badge">
                                    {{ optional($blog->published_at ?? $blog->created_at)->format('d M, Y') }}
                                </span>
                                @if ($blog->is_featured)
                                    <span class="blog-featured-badge">Featured</span>
                                @endif
                            </div>

                            <div class="blog-card-body">
                                <div class="blog-card-meta">
                                    <span>{{ $blog->author_name ?? 'Mithilanchal Farms' }}</span>
                                    @if ($blog->content_type)
                                        <span class="blog-card-dot">•</span>
                                        <span class="blog-card-type">{{ str_replace('_', ' ', $blog->content_type) }}</span>
                                    @endif
                                </div>

                                <h2 class="blog-card-title">{{ $blog->title }}</h2>

                                <p class="blog-card-excerpt">
                                    {{ Str::limit($blog->excerpt ?: strip_tags($blog->content), 140) }}
                                </p>

                                <span class="blog-card-cta">
                                    Read More
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="blog-empty">
                        <p>No published blog posts yet. Check back soon.</p>
                    </div>
                @endforelse
            </div>

            @if ($blogs->hasPages())
                <div class="blog-pagination">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layout>
