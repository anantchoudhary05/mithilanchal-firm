<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CityPage;

use App\Models\CityPage;
use App\MoonShine\Resources\CityPage\Pages\CityPageFormPage;
use App\MoonShine\Resources\CityPage\Pages\CityPageIndexPage;
use App\Support\CmsUser;
use Closure;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;

/**
 * @extends ModelResource<CityPage, CityPageIndexPage, CityPageFormPage, null>
 */
#[Icon('map-pin')]
#[Group('Content')]
#[Order(2)]
class CityPageResource extends ModelResource
{
    protected string $model = CityPage::class;

    protected string $column = 'city_name';

    protected string $sortColumn = 'city_name';

    protected SortDirection $sortDirection = SortDirection::ASC;

    protected array $with = ['sections'];

    public function getTitle(): string
    {
        return 'Location / City Pages';
    }

    public function canSee(): bool
    {
        return CmsUser::isAdmin();
    }

    protected function isCan(Ability $ability): bool
    {
        return CmsUser::isAdmin();
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            CityPageIndexPage::class,
            CityPageFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'city_name',
            'slug',
            'state',
            'seo_title',
            'status',
        ];
    }

    public function getPreviewButton(): ActionButton
    {
        return ActionButton::make(
            '',
            static function (mixed $item, ?DataWrapperContract $data): string {
                $page = $item instanceof CityPage ? $item : $data?->getOriginal();

                if (! $page instanceof CityPage || blank($page->slug) || ! $page->exists) {
                    return '#';
                }

                return $page->previewUrl();
            },
        )
            ->blank()
            ->withoutLoading()
            ->info()
            ->icon('eye')
            ->class('btn-square')
            ->canSee(static fn (mixed $item): bool => $item instanceof CityPage && $item->exists && filled($item->slug))
            ->customAttributes([
                'title' => 'Preview',
                'aria-label' => 'Preview',
            ])
            ->showInLine();
    }

    /**
     * @param  Closure(CityPage): bool  $check
     */
    public function itemPasses(Closure $check): bool
    {
        $item = $this->getItem();

        if (! $item instanceof CityPage) {
            return true;
        }

        return $check($item);
    }
}
