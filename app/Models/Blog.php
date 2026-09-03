<?php

namespace App\Models;

use Database\Factories\BlogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Blog extends Model
{
    /** @use HasFactory<BlogFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'image_alt',
        'author_name',
        'author_profile',
        'author_id',
        'status',
        'content_type',
        'is_active',
        'is_featured',
        'is_sticky',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'link_attribute',
        'custom_schema',
        'heading_structure',
        'table_of_contents',
        'faq',
        'related_blog_ids',
        'related_products',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_sticky' => 'boolean',
            'published_at' => 'datetime',
            'faq' => 'array',
            'related_blog_ids' => 'array',
            'related_products' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Blog $blog): void {
            if (blank($blog->slug) && filled($blog->title)) {
                $blog->slug = static::uniqueSlug(Str::slug($blog->title), $blog->id);
            } elseif (filled($blog->slug)) {
                $blog->slug = static::uniqueSlug(Str::slug($blog->slug), $blog->id);
            }

            if (filled($blog->content)) {
                $blog->content = clean($blog->content);
                [$blog->content, $blog->table_of_contents] = static::injectHeadingAnchors($blog->content);
                $blog->heading_structure = $blog->heading_structure ?: static::buildHeadingStructure($blog->content);
            }

            if (blank($blog->meta_title) && filled($blog->title)) {
                $blog->meta_title = $blog->title;
            }

            if (blank($blog->excerpt) && filled($blog->content)) {
                $blog->excerpt = Str::limit(strip_tags($blog->content), 160);
            }

            if (blank($blog->image_alt) && filled($blog->title)) {
                $blog->image_alt = $blog->title;
            }

            $blog->syncAuthorDisplayFields();

            // Stamp publish date when missing on published posts.
            if ($blog->status === 'published' && blank($blog->published_at)) {
                $blog->published_at = Carbon::now();
            }
        });

        static::saved(fn () => static::clearBlogCaches());
        static::deleted(fn () => static::clearBlogCaches());
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $q): void {
                // Published posts are live immediately (published_at is display date only).
                $q->where('status', 'published')
                    // Scheduled posts go live when the schedule time is reached.
                    ->orWhere(function (Builder $scheduled): void {
                        $scheduled->where('status', 'scheduled')
                            ->whereNotNull('published_at')
                            ->where('published_at', '<=', now());
                    });
            });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class, 'author_id');
    }

    public function scopeAuthoredBy(Builder $query, int $authorId): Builder
    {
        return $query->where('author_id', $authorId);
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('status', 'review');
    }

    public function isAwaitingApproval(): bool
    {
        return $this->status === 'review';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'published' => 'Approved',
            'review' => 'Pending',
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            default => (string) $this->status,
        };
    }

    public function approve(): void
    {
        $this->status = 'published';
        $this->is_active = true;

        if (blank($this->published_at)) {
            $this->published_at = Carbon::now();
        }

        $this->save();
    }

    public function isLive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->status === 'published') {
            return true;
        }

        return $this->status === 'scheduled'
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function isOwnedBy(?int $userId): bool
    {
        return $userId !== null && (int) $this->author_id === $userId;
    }

    public function authorCanEdit(?int $userId): bool
    {
        return $this->isOwnedBy($userId);
    }

    public function authorCanDelete(?int $userId): bool
    {
        return $this->isOwnedBy($userId)
            && in_array($this->status, ['draft', 'review'], true);
    }

    public function previewUrl(): string
    {
        return route('cms.blogs.preview', $this);
    }

    public function getPreviewUrlAttribute(): string
    {
        return blank($this->slug) ? '#' : $this->previewUrl();
    }

    public function syncAuthorDisplayFields(): void
    {
        if (blank($this->author_id)) {
            return;
        }

        $author = $this->relationLoaded('author')
            ? $this->author
            : MoonshineUser::query()->find($this->author_id);

        if ($author === null) {
            return;
        }

        $this->author_name = $author->name;

        if (blank($this->author_profile) && filled($author->bio)) {
            $this->author_profile = $author->bio;
        }
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (blank($this->featured_image)) {
            return null;
        }

        return asset('storage/'.$this->featured_image);
    }

    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function relatedBlogs()
    {
        $ids = collect($this->related_blog_ids ?? [])->filter()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return static::published()
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Blog $blog) => $ids->search($blog->id))
            ->values();
    }

    public static function clearBlogCaches(): void
    {
        Cache::forget('blogs.sitemap');
    }

    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base ?: 'post';
        $original = $slug;
        $counter = 1;

        while (
            static::query()
                ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public static function injectHeadingAnchors(string $html): array
    {
        $tocItems = [];

        $content = preg_replace_callback(
            '/<h([2-3])([^>]*)>(.*?)<\/h\1>/is',
            function (array $match) use (&$tocItems): string {
                $level = (int) $match[1];
                $attrs = $match[2];
                $inner = $match[3];
                $text = trim(strip_tags($inner));

                if ($text === '') {
                    return $match[0];
                }

                $anchor = Str::slug($text);
                $pad = $level === 3 ? 'ms-3' : '';
                $tocItems[] = "<li class=\"{$pad}\"><a href=\"#{$anchor}\">".e($text).'</a></li>';

                if (preg_match('/\sid=(["\']).*?\1/i', $attrs)) {
                    return $match[0];
                }

                return '<h'.$level.$attrs.' id="'.$anchor.'">'.$inner.'</h'.$level.'>';
            },
            $html
        ) ?? $html;

        $toc = $tocItems === [] ? '' : '<ul class="blog-toc-list">'.implode('', $tocItems).'</ul>';

        return [$content, $toc];
    }

    public static function buildTableOfContents(string $html): string
    {
        return static::injectHeadingAnchors($html)[1];
    }

    public static function buildHeadingStructure(string $html): string
    {
        if (! preg_match_all('/<h([1-6])[^>]*>(.*?)<\/h\1>/is', $html, $matches, PREG_SET_ORDER)) {
            return '';
        }

        $lines = [];
        foreach ($matches as $match) {
            $text = trim(strip_tags($match[2]));
            if ($text !== '') {
                $lines[] = 'H'.$match[1].': '.$text;
            }
        }

        return implode("\n", $lines);
    }
}
