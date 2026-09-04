<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CityPage\Pages;

use App\Models\CityPage;
use App\MoonShine\Resources\CityPage\CityPageResource;
use App\MoonShine\Resources\CityPage\CityPageSectionFields;
use App\Support\CityPageBlueprint;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Url;

/**
 * @extends FormPage<CityPageResource, CityPage>
 */
final class CityPageFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make('Basic Information', [
                        ID::make(),
                        Flex::make([
                            Text::make('City name', 'city_name')
                                ->required()
                                ->hint('Used in the Location dropdown and as {city} in section text.'),
                            Text::make('State', 'state')
                                ->hint('Optional. Used as {state} in section text. Example: Bihar'),
                        ])->itemsAlign('start')->justifyAlign('start'),
                        Flex::make([
                            Text::make('URL slug', 'slug')
                                ->hint('Leave empty to generate from the city name. Public URL: /location/{slug}'),
                            Select::make('Template', 'template')
                                ->options(CityPageBlueprint::templates())
                                ->default(CityPageBlueprint::TEMPLATE_STANDARD)
                                ->required()
                                ->hint('Same content, three layouts. Template 1 is the full landing page.'),
                        ])->itemsAlign('start')->justifyAlign('start'),
                        Select::make('Status', 'status')
                            ->options(CityPageBlueprint::statuses())
                            ->default(CityPage::STATUS_DRAFT)
                            ->required()
                            ->hint('Draft stays out of the public Location menu. Use Preview, then Publish.'),
                    ])->icon('map-pin'),
                    Tab::make('Page Sections', [
                        ...CityPageSectionFields::all(),
                    ])->icon('squares-2x2'),
                    Tab::make('SEO', [
                        Flex::make([
                            Text::make('SEO title', 'seo_title')
                                ->hint('Browser tab and search-result title. Aim for under 70 characters. Leave empty to use the city name.'),
                            Text::make('Meta keywords', 'meta_keywords'),
                        ])->itemsAlign('start')->justifyAlign('start'),
                        Textarea::make('Meta description', 'meta_description')
                            ->hint('Aim for under 160 characters.'),
                        Url::make('Canonical URL', 'canonical_url')
                            ->hint('Leave empty to use /location/{slug}'),
                        Flex::make([
                            Text::make('Open Graph title', 'og_title')
                                ->hint('Leave empty to copy the SEO title.'),
                            Text::make('Open Graph description', 'og_description')
                                ->hint('Leave empty to copy the meta description.'),
                        ])->itemsAlign('start')->justifyAlign('start'),
                        Image::make('Open Graph image', 'og_image')
                            ->disk('public')
                            ->dir('city-pages')
                            ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                            ->removable()
                            ->hint('Optional social sharing image. Falls back to the hero image.'),
                    ])->icon('globe-alt'),
                ]),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        $page = $item->getOriginal();
        $ignoreId = $page instanceof CityPage ? $page->id : null;

        $rules = [
            'city_name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('city_pages', 'city_name')->ignore($ignoreId),
            ],
            'state' => ['nullable', 'string', 'max:120'],
            'slug' => [
                'nullable',
                'string',
                'max:160',
                Rule::unique('city_pages', 'slug')->ignore($ignoreId),
            ],
            'template' => ['required', 'in:'.implode(',', array_keys(CityPageBlueprint::templates()))],
            'status' => ['required', 'in:draft,published'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:180'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:500'],
            'og_title' => ['nullable', 'string', 'max:70'],
            'og_description' => ['nullable', 'string', 'max:180'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ];

        foreach (CityPageBlueprint::types() as $type) {
            $enabledKey = CityPageBlueprint::formKey($type).'.is_enabled';
            $rules[CityPageBlueprint::formKey($type).'.display_order'] = ['nullable', 'integer', 'min:0', 'max:9999'];
            $rules[CityPageBlueprint::formKey($type).'.is_enabled'] = ['nullable'];

            if ($type === CityPageBlueprint::HERO) {
                $rules[CityPageBlueprint::formKey($type).'.title'] = [
                    Rule::requiredIf(fn (): bool => request()->boolean($enabledKey)),
                    'nullable',
                    'string',
                    'max:255',
                ];
            }
        }

        return $rules;
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons()->prepend($this->getResource()->getPreviewButton());
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Delete', 'aria-label' => 'Delete']);
    }
}
