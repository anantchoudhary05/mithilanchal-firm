<?php

namespace App\Models;

use App\Support\CityPageBlueprint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CityPageSection extends Model
{
    protected $fillable = [
        'city_page_id',
        'section_type',
        'is_enabled',
        'display_order',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'display_order' => 'integer',
            'content' => 'array',
        ];
    }

    public function cityPage(): BelongsTo
    {
        return $this->belongsTo(CityPage::class);
    }

    public function label(): string
    {
        return CityPageBlueprint::label($this->section_type);
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        $content = is_array($this->content) ? $this->content : [];
        $page = $this->relationLoaded('cityPage') ? $this->cityPage : $this->cityPage()->first();

        if ($page instanceof CityPage) {
            return $page->interpolate($content);
        }

        return $content;
    }

    public function value(string $key, mixed $default = null): mixed
    {
        return data_get($this->data(), $key, $default);
    }

    public function filled(string $key): bool
    {
        return filled($this->value($key));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function items(): array
    {
        $items = $this->value('items', []);

        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(
            $items,
            fn (mixed $item): bool => is_array($item) && $this->itemHasContent($item),
        ));
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function itemHasContent(array $item): bool
    {
        foreach (['title', 'name', 'question', 'value', 'label', 'image', 'text', 'description'] as $key) {
            if (filled($item[$key] ?? null)) {
                return true;
            }
        }

        return false;
    }

    public function imageUrl(string $key = 'image'): ?string
    {
        return self::resolvePublicUrl($this->value($key));
    }

    public function iconClass(?string $icon = null): string
    {
        return CityPageBlueprint::iconClass((string) ($icon ?: $this->value('icon', 'leaf')));
    }

    /**
     * @return array<string, mixed>
     */
    public function toFormArray(): array
    {
        return array_merge(
            is_array($this->content) ? $this->content : [],
            [
                'is_enabled' => $this->is_enabled,
                'display_order' => $this->display_order,
            ],
        );
    }

    public static function resolvePublicUrl(?string $path): ?string
    {
        return HomepageSection::resolvePublicUrl($path);
    }

    public static function resolveLink(?string $url): ?string
    {
        return HomepageSection::resolveLink($url);
    }
}
