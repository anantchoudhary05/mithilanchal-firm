<?php

namespace App\Models;

use App\Support\CityPageBlueprint;
use Database\Factories\CityPageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CityPage extends Model
{
    /** @use HasFactory<CityPageFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const NAV_CACHE_KEY = 'city_pages.nav.rows';

    /**
     * Incoming MoonShine section payloads, keyed by section type.
     *
     * @var array<string, mixed>|null
     */
    public ?array $pendingSectionInput = null;

    protected $fillable = [
        'city_name',
        'state',
        'slug',
        'template',
        'status',
        'seo_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (CityPage $page): void {
            $page->pendingSectionInput = $page->extractSectionInput();

            if (blank($page->slug) && filled($page->city_name)) {
                $page->slug = static::uniqueSlug(Str::slug($page->city_name), $page->id);
            } elseif (filled($page->slug)) {
                $page->slug = static::uniqueSlug(Str::slug($page->slug), $page->id);
            }

            if (! in_array($page->template, array_keys(CityPageBlueprint::templates()), true)) {
                $page->template = CityPageBlueprint::TEMPLATE_STANDARD;
            }

            if (! in_array($page->status, [self::STATUS_DRAFT, self::STATUS_PUBLISHED], true)) {
                $page->status = self::STATUS_DRAFT;
            }

            if ($page->status === self::STATUS_PUBLISHED && blank($page->published_at)) {
                $page->published_at = Carbon::now();
            }

            if (blank($page->seo_title) && filled($page->city_name)) {
                $page->seo_title = $page->defaultSeoTitle();
            }
        });

        static::saved(function (CityPage $page): void {
            if (is_array($page->pendingSectionInput) && $page->pendingSectionInput !== []) {
                $page->syncSections($page->pendingSectionInput);
            }

            $page->ensureSections();
            $page->pendingSectionInput = null;
            static::clearCaches();
        });

        static::deleted(fn () => static::clearCaches());
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CityPageSection::class)->orderBy('display_order')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function publish(): void
    {
        $this->status = self::STATUS_PUBLISHED;
        $this->published_at = $this->published_at ?: Carbon::now();
        $this->save();
    }

    public function unpublish(): void
    {
        $this->status = self::STATUS_DRAFT;
        $this->save();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function publicUrl(): string
    {
        return route('location.show', $this->slug);
    }

    public function previewUrl(): string
    {
        return route('cms.locations.preview', $this);
    }

    public function templateLabel(): string
    {
        return CityPageBlueprint::templates()[$this->template] ?? $this->template;
    }

    public function templateView(): string
    {
        return match ($this->template) {
            CityPageBlueprint::TEMPLATE_MODERN => 'location.templates.modern',
            CityPageBlueprint::TEMPLATE_MINIMAL => 'location.templates.minimal',
            default => 'location.templates.standard',
        };
    }

    public function seoTitle(): string
    {
        return filled($this->seo_title) ? (string) $this->seo_title : $this->defaultSeoTitle();
    }

    public function ogTitle(): string
    {
        return filled($this->og_title) ? (string) $this->og_title : $this->seoTitle();
    }

    public function ogDescription(): string
    {
        return filled($this->og_description) ? (string) $this->og_description : (string) ($this->meta_description ?: '');
    }

    public function ogImageUrl(): ?string
    {
        return CityPageSection::resolvePublicUrl($this->og_image)
            ?: $this->sectionByType(CityPageBlueprint::HERO)?->imageUrl('image');
    }

    public function canonicalUrl(): string
    {
        return filled($this->canonical_url) ? (string) $this->canonical_url : $this->publicUrl();
    }

    public function defaultSeoTitle(): string
    {
        return 'Premium '.$this->city_name.' Makhana Supplier | Mithilanchal Farms';
    }

    public function sectionByType(string $type): ?CityPageSection
    {
        $this->loadMissing('sections');

        return $this->sections->firstWhere('section_type', $type);
    }

    /**
     * @return Collection<int, CityPageSection>
     */
    public function enabledOrderedSections(): Collection
    {
        $this->loadMissing('sections');

        return $this->sections
            ->filter(fn (CityPageSection $section): bool => $section->is_enabled)
            ->sortBy([
                ['display_order', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    public function formSection(string $type): array
    {
        $section = $this->sectionByType($type);

        if (! $section instanceof CityPageSection) {
            return CityPageBlueprint::defaultFormSection($type);
        }

        return $section->toFormArray();
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function syncSections(array $input): void
    {
        foreach (CityPageBlueprint::types() as $type) {
            $payload = $input[$type] ?? null;

            if (! is_array($payload)) {
                continue;
            }

            $current = $this->sections()->where('section_type', $type)->first();
            $enabled = array_key_exists('is_enabled', $payload)
                ? filter_var($payload['is_enabled'], FILTER_VALIDATE_BOOLEAN)
                : (bool) ($current?->is_enabled ?? CityPageBlueprint::defaultEnabled($type));
            $order = (int) ($payload['display_order'] ?? CityPageBlueprint::defaultOrder($type));
            unset($payload['is_enabled'], $payload['display_order']);

            if (isset($payload['body']) && is_string($payload['body']) && $payload['body'] !== '') {
                $payload['body'] = clean($payload['body']);
            }

            $this->sections()->updateOrCreate(
                ['section_type' => $type],
                [
                    'is_enabled' => $enabled,
                    'display_order' => $order,
                    'content' => $payload,
                ],
            );
        }

        $this->unsetRelation('sections');
    }

    public function ensureSections(): void
    {
        foreach (CityPageBlueprint::types() as $type) {
            $this->sections()->firstOrCreate(
                ['section_type' => $type],
                [
                    'is_enabled' => CityPageBlueprint::defaultEnabled($type),
                    'display_order' => CityPageBlueprint::defaultOrder($type),
                    'content' => CityPageBlueprint::defaultContent($type),
                ],
            );
        }

        $this->unsetRelation('sections');
    }

    /**
     * @return array<string, mixed>
     */
    public function extractSectionInput(): array
    {
        $input = [];
        $attributes = $this->getAttributes();

        foreach (CityPageBlueprint::types() as $type) {
            $key = CityPageBlueprint::formKey($type);

            if (! array_key_exists($key, $attributes)) {
                continue;
            }

            $value = $attributes[$key];
            unset($attributes[$key]);

            if (is_string($value)) {
                $decoded = json_decode($value, true);
                $value = is_array($decoded) ? $decoded : [];
            }

            if (is_array($value)) {
                $input[$type] = $value;
            }
        }

        $this->setRawAttributes($attributes);

        return $input;
    }

    public function interpolate(mixed $value): mixed
    {
        if (is_string($value)) {
            return strtr($value, [
                '{city}' => (string) $this->city_name,
                '{state}' => (string) ($this->state ?: 'Bihar'),
            ]);
        }

        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->interpolate($item), $value);
        }

        return $value;
    }

    public function highlightedHeading(string $title, ?string $highlight = null, string $class = 'city-highlight'): string
    {
        $safeTitle = e((string) $this->interpolate($title));
        $needle = trim((string) $this->interpolate((string) $highlight));

        if ($needle === '') {
            return $safeTitle;
        }

        $safeNeedle = e($needle);

        if ($safeNeedle !== '' && str_contains($safeTitle, $safeNeedle)) {
            return str_replace($safeNeedle, '<span class="'.$class.'">'.$safeNeedle.'</span>', $safeTitle);
        }

        return $safeTitle;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonLd(): array
    {
        $faqSection = $this->sectionByType(CityPageBlueprint::FAQ);
        $faqItems = ($faqSection instanceof CityPageSection && $faqSection->is_enabled)
            ? $faqSection->items()
            : [];
        $mainEntity = [];

        foreach ($faqItems as $item) {
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $this->interpolate($question),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $this->interpolate($answer),
                ],
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $this->seoTitle(),
            'description' => $this->meta_description,
            'url' => $this->canonicalUrl(),
            'about' => [
                '@type' => 'Place',
                'name' => $this->city_name,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $this->city_name,
                    'addressRegion' => $this->state ?: 'Bihar',
                    'addressCountry' => 'IN',
                ],
            ],
        ];

        if ($mainEntity !== []) {
            $schema['mainEntity'] = $mainEntity;
        }

        return $schema;
    }

    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = trim($base) !== '' ? $base : 'city';
        $slug = $base;
        $i = 1;

        while (static::query()
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    /**
     * @return Collection<int, self>
     */
    public static function navCities(): Collection
    {
        if (! self::tableReady()) {
            return collect();
        }

        $rows = null;

        try {
            $cached = Cache::get(self::NAV_CACHE_KEY);
            $rows = is_array($cached) ? $cached : null;
        } catch (\Throwable) {
            $rows = null;
        }

        // Database/file cache cannot safely store Eloquent collections.
        if ($rows === null) {
            Cache::forget(self::NAV_CACHE_KEY);
            Cache::forget('city_pages.nav');

            $rows = static::query()
                ->published()
                ->orderBy('city_name')
                ->get(['id', 'city_name', 'slug', 'state'])
                ->map(fn (self $city): array => $city->only(['id', 'city_name', 'slug', 'state']))
                ->values()
                ->all();

            Cache::put(self::NAV_CACHE_KEY, $rows, now()->addHour());
        }

        return collect($rows)
            ->filter(fn (mixed $row): bool => is_array($row) && filled($row['slug'] ?? null))
            ->map(fn (array $row): self => new static($row))
            ->values();
    }

    public static function clearCaches(): void
    {
        Cache::forget(self::NAV_CACHE_KEY);
        Cache::forget('city_pages.nav');
        Cache::forget('blogs.sitemap');
    }

    public static function tableReady(): bool
    {
        return Schema::hasTable((new static)->getTable());
    }
}
