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
            @if (! empty($isPreview))
                <div class="blog-preview-banner" role="status">
                    CMS preview — visitors cannot see this page unless the post is approved and active.
                </div>
            @endif

            <a href="{{ route('blog.index') }}" class="blog-back-link">← Back to Blog</a>

            <div class="blog-detail-layout">
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
                                decoding="async"
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
                </article>

                <aside class="blog-detail-sidebar" aria-label="Related reading and products">
                    @if ($relatedProducts->isNotEmpty())
                        <section class="blog-sidebar-card">
                            <h2>Related Products</h2>

                            <div
                                class="related-product-carousel{{ $relatedProducts->count() < 2 ? ' is-single' : '' }}"
                                data-product-carousel
                                aria-roledescription="carousel"
                                aria-label="Related products"
                            >
                                <div class="related-product-viewport">
                                    <div class="related-product-track">
                                        @foreach ($relatedProducts as $index => $product)
                                            <article
                                                class="related-product-slide{{ $index === 0 ? ' is-active' : '' }}"
                                                data-product-slide
                                                aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
                                                aria-label="{{ $product['title'] }}"
                                            >
                                                <div class="related-product-card">
                                                    <div class="related-product-media">
                                                        <img
                                                            src="{{ $product['image_url'] }}"
                                                            alt="{{ $product['title'] }}"
                                                            loading="lazy"
                                                            decoding="async"
                                                        >
                                                        @if (! empty($product['badge']))
                                                            <span class="related-product-badge">{{ $product['badge'] }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="related-product-body">
                                                        @if (! empty($product['grade']))
                                                            <span class="related-product-grade">{{ $product['grade'] }}</span>
                                                        @endif

                                                        <h3>{{ $product['title'] }}</h3>
                                                        <p>{{ $product['description'] }}</p>
                                                        <p class="related-product-origin">Sourced from Mithilanchal, Bihar</p>

                                                        <a href="{{ $product['enquiry_url'] }}" class="related-product-enquire">
                                                            Enquire Now
                                                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                                        </a>

                                                        <a href="{{ $product['url'] }}" class="related-product-link">
                                                            View product details
                                                        </a>
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($relatedProducts->count() > 1)
                                    <div class="related-product-controls">
                                        <button type="button" class="related-product-nav" data-carousel-prev aria-label="Previous product">
                                            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                                        </button>
                                        <div class="related-product-dots" data-carousel-dots></div>
                                        <button type="button" class="related-product-nav" data-carousel-next aria-label="Next product">
                                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endif

                    @if ($relatedBlogs->isNotEmpty())
                        <section class="blog-sidebar-card">
                            <h2>Related Blogs</h2>
                            <div class="related-blog-list">
                                @foreach ($relatedBlogs as $related)
                                    <a class="related-blog-item" href="{{ route('blog.show', $related->slug) }}">
                                        <span class="related-blog-thumb">
                                            @if ($related->featured_image_url)
                                                <img
                                                    src="{{ $related->featured_image_url }}"
                                                    alt="{{ $related->image_alt ?: $related->title }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            @else
                                                <img
                                                    src="{{ asset('assests/img/hq-bowl.jpg') }}"
                                                    alt="{{ $related->title }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            @endif
                                        </span>
                                        <span class="related-blog-copy">
                                            <strong>{{ $related->title }}</strong>
                                            <span class="related-blog-more">Read more</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>
            </div>
        </div>
    </section>
</x-layout>
