<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Blog;

use App\Models\Blog;
use App\MoonShine\Resources\Blog\Pages\BlogFormPage;
use App\MoonShine\Resources\Blog\Pages\BlogIndexPage;
use App\Support\CmsUser;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;

/**
 * @extends ModelResource<Blog, BlogIndexPage, BlogFormPage, null>
 */
#[Icon('newspaper')]
#[Group('Content')]
#[Order(1)]
class BlogResource extends ModelResource
{
    protected string $model = Blog::class;

    protected string $column = 'title';

    protected array $with = ['author'];

    protected bool $simplePaginate = false;

    public function getTitle(): string
    {
        return CmsUser::isAuthor() ? 'My Blogs' : 'Blogs';
    }

    protected function activeActions(): ListOf
    {
        $actions = parent::activeActions()->except(Action::VIEW);

        if (CmsUser::isAuthor()) {
            return $actions->except(Action::MASS_DELETE);
        }

        return $actions;
    }

    protected function pages(): array
    {
        return [
            BlogIndexPage::class,
            BlogFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
            'slug',
            'meta_title',
            'author_name',
            'status',
        ];
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        if (CmsUser::isAuthor() && CmsUser::id() !== null) {
            return $builder->where('author_id', CmsUser::id());
        }

        return $builder;
    }

    protected function isCan(Ability $ability): bool
    {
        if (! CmsUser::check()) {
            return false;
        }

        if (CmsUser::isAdmin()) {
            return true;
        }

        if (! CmsUser::isAuthor()) {
            return false;
        }

        return match ($ability) {
            Ability::VIEW_ANY, Ability::CREATE, Ability::VIEW => true,
            Ability::UPDATE => $this->authorMay(static fn (Blog $blog): bool => $blog->authorCanEdit(CmsUser::id())),
            Ability::DELETE => $this->authorMay(static fn (Blog $blog): bool => $blog->authorCanDelete(CmsUser::id())),
            default => false,
        };
    }

    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        $this->applyRoleRules($item, creating: true);

        return $item;
    }

    protected function beforeUpdating(DataWrapperContract $item): DataWrapperContract
    {
        $this->applyRoleRules($item, creating: false);

        return $item;
    }

    private function applyRoleRules(DataWrapperContract $item, bool $creating): void
    {
        /** @var Blog $blog */
        $blog = $item->getOriginal();

        if (CmsUser::isAuthor()) {
            $blog->author_id = CmsUser::id();

            if (! in_array($blog->status, ['draft', 'review'], true)) {
                $blog->status = $creating ? 'draft' : 'review';
            }

            $blog->is_active = false;
            $blog->is_featured = false;

            return;
        }

        if (blank($blog->author_id) && CmsUser::id() !== null) {
            $blog->author_id = CmsUser::id();
        }
    }

    public function getPreviewButton(): ActionButton
    {
        return ActionButton::make(
            '',
            static function (mixed $item, ?DataWrapperContract $data): string {
                $blog = $item instanceof Blog ? $item : $data?->getOriginal();

                if (! $blog instanceof Blog || blank($blog->slug)) {
                    return '#';
                }

                return $blog->previewUrl();
            },
        )
            ->blank()
            ->withoutLoading()
            ->info()
            ->icon('eye')
            ->class('btn-square')
            ->canSee(static fn (mixed $item): bool => $item instanceof Blog && filled($item->slug))
            ->customAttributes([
                'title' => 'Preview',
                'aria-label' => 'Preview',
            ])
            ->showInLine();
    }

    /**
     * @param  Closure(Blog): bool  $check
     */
    private function authorMay(Closure $check): bool
    {
        $item = $this->getItem();

        if (! $item instanceof Blog) {
            return true;
        }

        return $check($item);
    }
}
