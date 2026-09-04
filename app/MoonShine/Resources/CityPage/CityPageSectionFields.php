<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CityPage;

use App\Models\CityPage;
use App\Support\CityPageBlueprint;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

final class CityPageSectionFields
{
    /**
     * @return list<Collapse>
     */
    public static function all(): array
    {
        $blocks = [];

        foreach (CityPageBlueprint::definitions() as $type => $meta) {
            $blocks[] = Collapse::make($meta['label'], self::fieldsFor($type, $meta['label']))
                ->icon('bars-3-bottom-left');
        }

        return $blocks;
    }

    /**
     * @return list<mixed>
     */
    public static function fieldsFor(string $type, string $label = 'Section'): array
    {
        $key = CityPageBlueprint::formKey($type);

        $common = [
            Switcher::make('Enable this section', 'is_enabled')
                ->default(CityPageBlueprint::defaultEnabled($type))
                ->hint('OFF sections are not rendered on the website.'),
            Number::make('Display order', 'display_order')
                ->buttons()
                ->min(0)
                ->default(CityPageBlueprint::defaultOrder($type))
                ->hint('Lower numbers appear first. Use this to reorder sections.'),
        ];

        $json = Json::make($label, $key)
            ->object()
            ->fields([
                ...$common,
                ...self::contentFields($type),
            ])
            ->changeFill(static function (mixed $data) use ($type): array {
                return $data instanceof CityPage
                    ? $data->formSection($type)
                    : CityPageBlueprint::defaultFormSection($type);
            });

        return [$json];
    }

    /**
     * @return list<mixed>
     */
    private static function contentFields(string $type): array
    {
        return match ($type) {
            CityPageBlueprint::HERO => self::heroFields(),
            CityPageBlueprint::STATS => [
                Json::make('Statistics', 'items')
                    ->fields([
                        Text::make('Value', 'value')->hint('e.g. 500+'),
                        Text::make('Label', 'label')->hint('e.g. Happy Buyers'),
                    ])
                    ->creatable()
                    ->removable()
                    ->reorderable(),
            ],
            CityPageBlueprint::ABOUT => self::aboutFields(),
            CityPageBlueprint::HIGHLIGHTS => [
                Text::make('Section title', 'title'),
                self::iconCardRepeater('Highlight items', 'items', withUrl: false),
            ],
            CityPageBlueprint::ATTRACTIONS => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Section title', 'title'),
                Textarea::make('Intro', 'description'),
                Json::make('Places', 'items')
                    ->fields([
                        Text::make('Name', 'name'),
                        Textarea::make('Short description', 'description'),
                        Image::make('Image', 'image')
                            ->disk('public')
                            ->dir('city-pages')
                            ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                            ->removable(),
                        Text::make('Image alt text', 'image_alt'),
                        Text::make('URL', 'url'),
                        Text::make('Location info', 'location'),
                    ])
                    ->vertical()
                    ->creatable()
                    ->removable()
                    ->reorderable(),
            ],
            CityPageBlueprint::SERVICES => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Section title', 'title'),
                Textarea::make('Intro', 'description'),
                self::iconCardRepeater('Service cards', 'items', withUrl: true),
            ],
            CityPageBlueprint::WHY_CHOOSE => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Section title', 'title'),
                Textarea::make('Intro', 'description'),
                Json::make('Benefit cards', 'items')
                    ->fields([
                        self::iconSelect(),
                        Text::make('Title', 'title'),
                        Textarea::make('Description', 'description'),
                        Switcher::make('Highlight this card', 'featured'),
                    ])
                    ->vertical()
                    ->creatable()
                    ->removable()
                    ->reorderable(),
            ],
            CityPageBlueprint::PROCESS => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Section title', 'title'),
                Text::make('Title highlight', 'title_highlight')
                    ->hint('Words shown in gold. Leave empty to skip.'),
                Textarea::make('Intro', 'description'),
                Json::make('Steps', 'items')
                    ->fields([
                        Text::make('Step number', 'number')->hint('e.g. 01'),
                        self::iconSelect(),
                        Text::make('Title', 'title'),
                        Textarea::make('Description', 'description'),
                    ])
                    ->vertical()
                    ->creatable()
                    ->removable()
                    ->reorderable(),
            ],
            CityPageBlueprint::NUTRITION => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Section title', 'title'),
                Text::make('Title highlight', 'title_highlight'),
                Textarea::make('Intro', 'description'),
                self::iconCardRepeater('Nutrition cards', 'items', withUrl: false),
            ],
            CityPageBlueprint::GALLERY => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Section title', 'title'),
                Json::make('Images', 'items')
                    ->fields([
                        Image::make('Image', 'image')
                            ->disk('public')
                            ->dir('city-pages')
                            ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                            ->removable(),
                        Text::make('Alt text', 'alt'),
                        Text::make('Caption', 'caption'),
                    ])
                    ->creatable()
                    ->removable()
                    ->reorderable(),
            ],
            CityPageBlueprint::TESTIMONIALS => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Section title', 'title'),
                Text::make('Title highlight', 'title_highlight')
                    ->hint('Usually the city name.'),
                Json::make('Reviews', 'items')
                    ->fields([
                        Text::make('Customer name', 'name'),
                        Text::make('Designation / location', 'designation'),
                        Textarea::make('Review', 'review'),
                        Number::make('Rating', 'rating')->min(1)->max(5)->default(5),
                        Image::make('Profile image', 'image')
                            ->disk('public')
                            ->dir('city-pages')
                            ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                            ->removable()
                            ->hint('Optional. Initials are used when empty.'),
                    ])
                    ->vertical()
                    ->creatable()
                    ->removable()
                    ->reorderable(),
            ],
            CityPageBlueprint::FAQ => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Section title', 'title'),
                Text::make('Title highlight', 'title_highlight'),
                Textarea::make('Intro', 'description'),
                Json::make('Questions', 'items')
                    ->fields([
                        Text::make('Question', 'question'),
                        Textarea::make('Answer', 'answer'),
                    ])
                    ->vertical()
                    ->creatable()
                    ->removable()
                    ->reorderable(),
            ],
            CityPageBlueprint::ADDITIONAL => [
                Text::make('Section title', 'title'),
                TinyMce::make('Content', 'body')
                    ->hint('Use this for extra city information that does not fit the other sections.'),
            ],
            CityPageBlueprint::CTA => [
                Text::make('Eyebrow', 'eyebrow'),
                Text::make('Heading', 'title'),
                Textarea::make('Description', 'description'),
                Text::make('Primary button text', 'primary_cta_text'),
                Text::make('Primary button URL', 'primary_cta_url'),
                Text::make('Secondary button text', 'secondary_cta_text'),
                Text::make('Secondary button URL', 'secondary_cta_url'),
                Text::make('WhatsApp button text', 'whatsapp_text'),
                Text::make('WhatsApp URL', 'whatsapp_url'),
                Image::make('Background image', 'background_image')
                    ->disk('public')
                    ->dir('city-pages')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                    ->removable()
                    ->hint('Optional. Leave empty for the solid green background.'),
            ],
            CityPageBlueprint::LOCATION_BAR => [
                Text::make('Title', 'title'),
                Textarea::make('Description', 'description'),
                Text::make('Primary button text', 'primary_cta_text'),
                Text::make('Primary button URL', 'primary_cta_url'),
                Text::make('Secondary button text', 'secondary_cta_text'),
                Text::make('Secondary button URL', 'secondary_cta_url'),
            ],
            default => [],
        };
    }

    /**
     * @return list<mixed>
     */
    private static function heroFields(): array
    {
        return [
            Text::make('Location tag', 'tag')
                ->hint('e.g. DARBHANGA, BIHAR — PREMIUM MAKHANA HUB. You can use {city} and {state}.'),
            Text::make('Hero title', 'title'),
            Textarea::make('Description', 'description'),
            Image::make('Hero image', 'image')
                ->disk('public')
                ->dir('city-pages')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                ->removable(),
            Text::make('Image alt text', 'image_alt'),
            Text::make('Primary button text', 'primary_cta_text'),
            Text::make('Primary button URL', 'primary_cta_url')
                ->hint('tel:+919296918101, /contact-us, or a full URL'),
            Text::make('Secondary button text', 'secondary_cta_text'),
            Text::make('Secondary button URL', 'secondary_cta_url'),
            Json::make('Floating badges', 'badges')
                ->fields([
                    self::iconSelect(),
                    Text::make('Title', 'title'),
                    Text::make('Subtext', 'subtext'),
                ])
                ->creatable()
                ->removable(),
        ];
    }

    /**
     * @return list<mixed>
     */
    private static function aboutFields(): array
    {
        return [
            Text::make('Eyebrow', 'eyebrow'),
            Text::make('Section title', 'title'),
            Textarea::make('Description', 'description'),
            Image::make('Primary image', 'image')
                ->disk('public')
                ->dir('city-pages')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                ->removable(),
            Text::make('Primary image alt', 'image_alt'),
            Image::make('Inset image', 'inset_image')
                ->disk('public')
                ->dir('city-pages')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                ->removable(),
            Text::make('Inset image alt', 'inset_image_alt'),
            Text::make('Ribbon badge', 'badge_text'),
            Text::make('Optional button text', 'button_text'),
            Text::make('Optional button URL', 'button_url'),
        ];
    }

    private static function iconSelect(): Select
    {
        return Select::make('Icon', 'icon')
            ->options(CityPageBlueprint::icons())
            ->searchable();
    }

    private static function iconCardRepeater(string $label, string $column, bool $withUrl): Json
    {
        $fields = [
            self::iconSelect(),
            Text::make('Title', 'title'),
            Textarea::make('Description', 'description'),
        ];

        if ($withUrl) {
            $fields[] = Text::make('Link', 'url');
        }

        return Json::make($label, $column)
            ->fields($fields)
            ->vertical()
            ->creatable()
            ->removable()
            ->reorderable();
    }
}
