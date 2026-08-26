<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Blog;

use App\Models\Blog;
use App\MoonShine\Resources\Blog\Pages\BlogFormPage;
use App\MoonShine\Resources\Blog\Pages\BlogIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\ListOf;
use MoonShine\Support\Enums\Action;

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

    protected bool $simplePaginate = false;

    public function getTitle(): string
    {
        return 'Blogs';
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
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
}
