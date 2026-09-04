<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Homepage\Pages;

use App\MoonShine\Resources\Homepage\HeroBannerResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<HeroBannerResource>
 */
final class HeroBannerFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Banner page', [
                ID::make(),
                Flex::make([
                    Text::make('Admin title', 'name')
                        ->required()
                        ->hint('Only shown in the CMS list, not on the website.'),
                    Number::make('Carousel order', 'sort_order')
                        ->buttons()
                        ->min(0)
                        ->default(10)
                        ->hint('Lower numbers appear first. Offer pages use the same order in the hero carousel.'),
                ])->itemsAlign('start')->justifyAlign('start'),
                Switcher::make('Active (visible on website)', 'is_active')
                    ->default(true),
            ]),
            Box::make('Hero template', [
                Text::make('Eyebrow', 'eyebrow')
                    ->hint('Small line above the headline, e.g. FROM MITHILA\'S PONDS'),
                Flex::make([
                    Text::make('Headline', 'headline')
                        ->required(),
                    Text::make('Headline highlight', 'headline_highlight')
                        ->hint('Optional second line shown in gold.'),
                ])->itemsAlign('start')->justifyAlign('start'),
                Textarea::make('Description', 'description'),
                Image::make('Background image', 'background_image')
                    ->disk('public')
                    ->dir('homepage')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                    ->removable()
                    ->hint('Wide landscape photo works best.'),
            ]),
            Box::make('Buttons', [
                Flex::make([
                    Text::make('Primary button text', 'button_text'),
                    Text::make('Primary button URL', 'button_url')
                        ->hint('Example: /product or /our-story'),
                ])->itemsAlign('start')->justifyAlign('start'),
                Flex::make([
                    Text::make('Secondary button text', 'button_2_text'),
                    Text::make('Secondary button URL', 'button_2_url')
                        ->hint('Example: /contact-us'),
                ])->itemsAlign('start')->justifyAlign('start'),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:255'],
            'headline_highlight' => ['nullable', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'button_2_text' => ['nullable', 'string', 'max:120'],
            'button_2_url' => ['nullable', 'string', 'max:500'],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ];
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Delete', 'aria-label' => 'Delete']);
    }
}
