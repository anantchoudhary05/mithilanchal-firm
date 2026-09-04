<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Homepage\Pages;

use App\MoonShine\Resources\Homepage\OfferPageResource;
use App\Support\SerialNumber;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<OfferPageResource>
 */
final class OfferPageIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            SerialNumber::forIndexPage($this),
            ID::make()->sortable()->columnSelection(hideOnInit: true),
            Image::make('Image', 'product_image')
                ->disk('public')
                ->dir('homepage')
                ->modifyRawValue(fn (?string $raw): string => $raw ?? ''),
            Text::make('Admin title', 'name')->sortable(),
            Text::make('Headline', 'headline'),
            Text::make('Code', 'coupon_code'),
            Number::make('Order', 'sort_order')->sortable(),
            Switcher::make('Popup', 'show_as_popup')->updateOnPreview(),
            Switcher::make('Active', 'is_active')->updateOnPreview(),
        ];
    }

    /**
     * @param  TableBuilder  $component
     */
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->stickyButtons()->columnSelection();
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Edit', 'aria-label' => 'Edit']);
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Delete', 'aria-label' => 'Delete']);
    }
}
