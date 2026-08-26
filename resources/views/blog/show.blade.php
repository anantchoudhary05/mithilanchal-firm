<x-layout
    :meta_title="$meta_title"
    :meta_description="$meta_description"
    :meta_keywords="$meta_keywords"
    :canonical_url="$canonical_url"
    :robots="$robots"
    :custom_schema="$custom_schema"
    og_type="article"
>
    <section class="blog-detail-wrap">
        <div class="container">
            <a href="{{ route('blog.index') }}" class="blog-back-link">← Back to Blog</a>

            <article class="blog-detail-card">
                <header class="blog-detail-header">
                    @if ($blog->content_type)
                        <span class="blog-type-badge">{{ str_replace('_', ' ', $blog->content_type) }}</span>
                    @endif

                    <h1 class="blog-detail-title">{{ $blog->title }}</h1>

                    <div class="blog-detail-meta">
                        <span>By <strong>{{ $blog->author_name ?? 'Admin' }}</strong></span>
                        <span class="blog-card-dot">•</span>
                        <time datetime="{{ optional($blog->published_at ?? $blog->created_at)->toIso8601String() }}">
                            {{ optional($blog->published_at ?? $blog->created_at)->format('F d, Y') }}
                        </time>
                        @if ($blog->updated_at && ! $blog->updated_at->equalTo($blog->created_at))
                            <span class="blog-card-dot">•</span>
                            <span>Updated {{ $blog->updated_at->format('F d, Y') }}</span>
                        @endif
                    </div>

                    @if ($blog->author_profile)
                        <p class="blog-author-profile">{{ $blog->author_profile }}</p>
                    @endif
                </header>

                @if ($blog->featured_image_url)
                    <figure class="blog-detail-image">
                        <img
                            src="{{ $blog->featured_image_url }}"
                            alt="{{ $blog->image_alt ?: $blog->title }}"
                        >
                    </figure>
                @endif

                @if ($blog->table_of_contents)
                    <aside class="blog-toc">
                        <h2>Table of Contents</h2>
                        {!! $blog->table_of_contents !!}
                    </aside>
                @endif

                <div class="blog-detail-content">
                    {!! $blog->content !!}
                </div>

                @if (!empty($blog->faq))
                    <section class="blog-detail-block">
                        <h2>FAQ</h2>
                        <div class="blog-faq">
                            @foreach ($blog->faq as $item)
                                @continue(empty($item['question']))
                                <details>
                                    <summary>{{ $item['question'] }}</summary>
                                    <p>{{ $item['answer'] ?? '' }}</p>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if (!empty($blog->related_products))
                    <section class="blog-detail-block">
                        <h2>Related Products</h2>
                        <div class="related-products">
                            @foreach ($blog->related_products as $product)
                                @continue(empty($product['title']))
                                <a class="related-chip" href="{{ $product['url'] ?? route('Product') }}">
                                    {{ $product['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($relatedBlogs->isNotEmpty())
                    <section class="blog-detail-block">
                        <h2>Related Blogs</h2>
                        <div class="related-blogs-grid">
                            @foreach ($relatedBlogs as $related)
                                <a class="related-blog-card" href="{{ route('blog.show', $related->slug) }}">
                                    <strong>{{ $related->title }}</strong>
                                    <span>{{ Str::limit($related->excerpt ?: strip_tags($related->content), 90) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>
        </div>
    </section>
</x-layout>
