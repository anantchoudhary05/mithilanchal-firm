<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Homepage;

use App\Models\HomepageSection;
use App\MoonShine\Resources\Homepage\Pages\HeroBannerFormPage;
use App\MoonShine\Resources\Homepage\Pages\HeroBannerIndexPage;
use App\Support\CmsUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<HomepageSection, HeroBannerIndexPage, HeroBannerFormPage, null>
 */
#[Icon('photo')]
#[Group('Homepage')]
#[Order(0)]
class HeroBannerResource extends ModelResource
{
    protected string $model = HomepageSection::class;

    protected string $column = 'name';

    protected string $sortColumn = 'sort_order';

    protected SortDirection $sortDirection = SortDirection::ASC;

    public function getTitle(): string
    {
        return 'Banner pages';
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
            HeroBannerIndexPage::class,
            HeroBannerFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'headline',
            'eyebrow',
        ];
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder->where('type', HomepageSection::TYPE_HERO_BANNER);
    }

    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        /** @var HomepageSection $section */
        $section = $item->getOriginal();
        $section->type = HomepageSection::TYPE_HERO_BANNER;
        $section->show_as_popup = false;

        return $item;
    }

    protected function beforeUpdating(DataWrapperContract $item): DataWrapperContract
    {
        /** @var HomepageSection $section */
        $section = $item->getOriginal();
        $section->type = HomepageSection::TYPE_HERO_BANNER;
        $section->show_as_popup = false;

        return $item;
    }
}
