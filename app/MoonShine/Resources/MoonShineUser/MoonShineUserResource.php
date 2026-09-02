<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MoonShineUser;

use App\Models\MoonshineUser;
use App\MoonShine\Resources\MoonShineUser\Pages\MoonShineUserFormPage;
use App\MoonShine\Resources\MoonShineUser\Pages\MoonShineUserIndexPage;
use App\Support\CmsUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<MoonshineUser, MoonShineUserIndexPage, MoonShineUserFormPage, null>
 */
#[Icon('users')]
#[Group('People')]
#[Order(0)]
class MoonShineUserResource extends ModelResource
{
    protected string $model = MoonshineUser::class;

    protected string $column = 'name';

    protected array $with = ['moonshineUserRole'];

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Authors';
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
            MoonShineUserIndexPage::class,
            MoonShineUserFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'email',
        ];
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder->withCount([
            'blogs',
            'blogs as published_count' => static fn (Builder $query): Builder => $query
                ->where('status', 'published')
                ->where('is_active', true),
            'blogs as pending_count' => static fn (Builder $query): Builder => $query->where('status', 'review'),
        ]);
    }
}
