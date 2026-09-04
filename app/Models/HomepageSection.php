<?php

namespace App\Models;

use Database\Factories\HomepageSectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class HomepageSection extends Model
{
    /** @use HasFactory<HomepageSectionFactory> */
    use HasFactory;

    public const TYPE_HERO_BANNER = 'hero_banner';

    public const TYPE_OFFER = 'offer';

    /** @var list<string> Types that can appear in the home hero carousel. */
    public const CAROUSEL_TYPES = [
        self::TYPE_HERO_BANNER,
        self::TYPE_OFFER,
    ];

    public const FEATURE_ICONS = [
        'leaf' => 'fa-leaf',
        'seedling' => 'fa-seedling',
        'heart' => 'fa-heart',
        'star' => 'fa-star',
        'check' => 'fa-circle-check',
        'truck' => 'fa-truck',
        'wheat-awn' => 'fa-wheat-awn',
        'bowl-food' => 'fa-bowl-food',
    ];

    protected $fillable = [
        'type',
        'name',
        'is_active',
        'sort_order',
        'show_as_popup',
        'eyebrow',
        'headline',
        'headline_highlight',
        'description',
        'background_image',
        'button_text',
        'button_url',
        'button_2_text',
        'button_2_url',
        'tagline',
        'discount_text',
        'discount_subtext',
        'coupon_code',
        'product_image',
        'bowl_image',
        'badge_text',
        'shipping_text',
        'urgency_text',
        'social_proof',
        'features',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_as_popup' => 'boolean',
            'sort_order' => 'integer',
            'features' => 'array',
            'payload' => 'array',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeBanners(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_HERO_BANNER);
    }

    public function scopeOffers(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_OFFER);
    }

    public function scopeCarousel(Builder $query): Builder
    {
        return $query->whereIn('type', self::CAROUSEL_TYPES);
    }

    public function isHeroBanner(): bool
    {
        return $this->type === self::TYPE_HERO_BANNER;
    }

    public function isOffer(): bool
    {
        return $this->type === self::TYPE_OFFER;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_HERO_BANNER => 'Banner page',
            self::TYPE_OFFER => 'Offer page',
            default => (string) $this->type,
        };
    }

    public function backgroundImageUrl(): ?string
    {
        return self::resolvePublicUrl($this->background_image);
    }

    public function productImageUrl(): ?string
    {
        return self::resolvePublicUrl($this->product_image);
    }

    public function bowlImageUrl(): ?string
    {
        return self::resolvePublicUrl($this->bowl_image);
    }

    public function carouselBackgroundImageUrl(): string
    {
        return $this->backgroundImageUrl()
            ?: $this->bowlImageUrl()
            ?: $this->productImageUrl()
            ?: asset('assests/img/hq-roast-bihar.jpg');
    }

    public function carouselEyebrow(): ?string
    {
        if (filled($this->eyebrow)) {
            return $this->eyebrow;
        }

        return filled($this->tagline) ? $this->tagline : 'SPECIAL OFFER';
    }

    public function carouselHighlight(): ?string
    {
        return filled($this->headline_highlight) ? $this->headline_highlight : $this->discount_text;
    }

    public function carouselDescription(): ?string
    {
        if (filled($this->description)) {
            return $this->description;
        }

        return collect([
            $this->discount_subtext,
            $this->shipping_text,
            $this->urgency_text,
        ])->filter()->implode(' · ') ?: null;
    }

    public function primaryButtonUrl(): ?string
    {
        return self::resolveLink($this->button_url);
    }

    public function secondaryButtonUrl(): ?string
    {
        return self::resolveLink($this->button_2_url);
    }

    /**
     * @return list<array{icon: string, icon_class: string, text: string}>
     */
    public function featureItems(): array
    {
        $items = collect($this->features ?? [])
            ->filter(fn (mixed $feature): bool => is_array($feature) && filled($feature['text'] ?? null))
            ->map(function (array $feature): array {
                $icon = (string) ($feature['icon'] ?? 'leaf');

                return [
                    'icon' => $icon,
                    'icon_class' => self::FEATURE_ICONS[$icon] ?? 'fa-leaf',
                    'text' => (string) $feature['text'],
                ];
            })
            ->values()
            ->all();

        return $items !== [] ? $items : self::defaultFeatures();
    }

    /**
     * @return list<array{icon: string, icon_class: string, text: string}>
     */
    public static function defaultFeatures(): array
    {
        return [
            ['icon' => 'leaf', 'icon_class' => 'fa-leaf', 'text' => '100% Natural & Premium'],
            ['icon' => 'seedling', 'icon_class' => 'fa-seedling', 'text' => 'Rich in Protein & Fiber'],
            ['icon' => 'heart', 'icon_class' => 'fa-heart', 'text' => 'Healthy Snacking Made Delicious'],
        ];
    }

    /**
     * @return Collection<int, self>
     */
    public static function carouselSlides(): Collection
    {
        if (! self::tableReady()) {
            return static::fallbackBanners();
        }

        $slides = static::query()
            ->active()
            ->carousel()
            ->ordered()
            ->get();

        $banners = $slides->filter(fn (self $section): bool => $section->isHeroBanner());

        if ($banners->isNotEmpty()) {
            return $slides->values();
        }

        return static::fallbackBanners()
            ->concat($slides->filter(fn (self $section): bool => $section->isOffer()))
            ->sortBy([
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
    }

    public static function activePopupOffer(): ?self
    {
        if (! self::tableReady()) {
            return null;
        }

        return static::query()
            ->active()
            ->offers()
            ->where('show_as_popup', true)
            ->ordered()
            ->first();
    }

    public static function tableReady(): bool
    {
        return Schema::hasTable((new static)->getTable());
    }

    /**
     * @return Collection<int, self>
     */
    public static function fallbackBanners(): Collection
    {
        return collect([
            new static([
                'type' => self::TYPE_HERO_BANNER,
                'name' => 'Welcome hero',
                'is_active' => true,
                'sort_order' => 10,
                'eyebrow' => "FROM MITHILA'S PONDS",
                'headline' => 'Rooted in Mithilanchal.',
                'headline_highlight' => 'Grown with care.',
                'description' => 'Premium fox nuts from Darbhanga — popped by local hands, graded with honesty, and shared with homes and businesses that value the real taste of Bihar.',
                'background_image' => 'assests/img/hq-roasted.jpg',
                'button_text' => 'Discover Our Story',
                'button_url' => '/our-story',
                'button_2_text' => 'Explore Products',
                'button_2_url' => '/product',
            ]),
        ]);
    }

    public static function resolvePublicUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'assests/') || str_starts_with($path, 'assets/') || str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }

    public static function resolveLink(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
            return $url;
        }

        return url($url);
    }
}
