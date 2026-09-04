<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Homepage\Pages;

use App\MoonShine\Resources\Homepage\OfferPageResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<OfferPageResource>
 */
final class OfferPageFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Offer page', [
                ID::make(),
                Flex::make([
                    Text::make('Admin title', 'name')
                        ->required()
                        ->hint('Only shown in the CMS list, not on the website.'),
                    Number::make('Carousel order', 'sort_order')
                        ->buttons()
                        ->min(0)
                        ->default(20)
                        ->hint('Uses the same order as banner pages in the home hero carousel.'),
                ])->itemsAlign('start')->justifyAlign('start'),
                Flex::make([
                    Switcher::make('Active (visible on website)', 'is_active')
                        ->default(true),
                    Switcher::make('Show as welcome popup', 'show_as_popup')
                        ->default(true)
                        ->hint('First visit shows the special offer popup. After it is closed, this offer appears as a matching hero slide in the banner carousel.'),
                ])->itemsAlign('center')->justifyAlign('start'),
            ]),
            Box::make('Offer template', [
                Text::make('Tagline', 'tagline')
                    ->hint('Script line at the top, e.g. Goodness in every bite!'),
                Text::make('Headline', 'headline')
                    ->required()
                    ->hint('e.g. Special Welcome Offer!'),
                Flex::make([
                    Text::make('Discount text', 'discount_text')
                        ->hint('e.g. 15% OFF'),
                    Text::make('Discount subtext', 'discount_subtext')
                        ->hint('e.g. ON YOUR FIRST ORDER'),
                ])->itemsAlign('start')->justifyAlign('start'),
                Text::make('Coupon code', 'coupon_code')
                    ->hint('e.g. MAKHANA15'),
                Json::make('Benefits', 'features')
                    ->fields([
                        Select::make('Icon', 'icon')
                            ->options([
                                'leaf' => 'Leaf',
                                'seedling' => 'Sprout',
                                'heart' => 'Heart',
                                'star' => 'Star',
                                'check' => 'Check',
                                'wheat-awn' => 'Wheat',
                                'bowl-food' => 'Bowl',
                            ]),
                        Text::make('Label', 'text'),
                    ])
                    ->creatable()
                    ->removable()
                    ->hint('Up to three short benefit lines work best.'),
            ]),
            Box::make('Product images', [
                Image::make('Carousel background image', 'background_image')
                    ->disk('public')
                    ->dir('homepage')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                    ->removable()
                    ->hint('Wide landscape photo for the home hero slide, so it matches the other banners. The popup still uses the pack and bowl images below.'),
                Image::make('Pack / product image', 'product_image')
                    ->disk('public')
                    ->dir('homepage')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                    ->removable()
                    ->hint('Used in the welcome popup only.'),
                Image::make('Bowl image', 'bowl_image')
                    ->disk('public')
                    ->dir('homepage')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                    ->removable()
                    ->hint('Used in the welcome popup only.'),
                Text::make('Quality seal text', 'badge_text')
                    ->hint('e.g. HEALTHY TASTY WHOLESOME'),
            ]),
            Box::make('Footer & button', [
                Text::make('Shipping text', 'shipping_text')
                    ->hint('e.g. Free Shipping On Orders Above ₹499'),
                Flex::make([
                    Text::make('Button text', 'button_text')
                        ->hint('e.g. SHOP NOW & SAVE'),
                    Text::make('Button URL', 'button_url')
                        ->hint('Example: /product'),
                ])->itemsAlign('start')->justifyAlign('start'),
                Text::make('Urgency text', 'urgency_text')
                    ->hint('Small line under the button.'),
                Textarea::make('Social proof', 'social_proof')
                    ->hint('e.g. Loved by 10,000+ Happy Customers'),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'discount_text' => ['nullable', 'string', 'max:80'],
            'discount_subtext' => ['nullable', 'string', 'max:120'],
            'coupon_code' => ['nullable', 'string', 'max:60'],
            'badge_text' => ['nullable', 'string', 'max:120'],
            'shipping_text' => ['nullable', 'string', 'max:255'],
            'urgency_text' => ['nullable', 'string', 'max:255'],
            'social_proof' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'product_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'bowl_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ];
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Delete', 'aria-label' => 'Delete']);
    }
}
