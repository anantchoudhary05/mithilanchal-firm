<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Blog;
use App\Models\MoonshineUser;
use App\MoonShine\Resources\Blog\BlogResource;
use App\MoonShine\Resources\Blog\Pages\BlogFormPage;
use App\Support\CmsRole;
use App\Support\CmsUser;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Text;

#[SkipMenu]
class Dashboard extends Page
{
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        return CmsUser::isAuthor() ? 'Author dashboard' : 'Admin dashboard';
    }

    protected function components(): iterable
    {
        return CmsUser::isAuthor()
            ? $this->authorComponents()
            : $this->adminComponents();
    }

    /**
     * @return list<ComponentContract>
     */
    private function adminComponents(): array
    {
        $pending = Blog::query()->pendingApproval()->count();
        $published = Blog::query()->where('status', 'published')->where('is_active', true)->count();
        $drafts = Blog::query()->where('status', 'draft')->count();
        $authors = MoonshineUser::query()
            ->where('moonshine_user_role_id', CmsRole::authorId())
            ->count();

        $authorRows = MoonshineUser::query()
            ->where('moonshine_user_role_id', CmsRole::authorId())
            ->withCount([
                'blogs',
                'blogs as published_count' => fn ($query) => $query->where('status', 'published')->where('is_active', true),
                'blogs as pending_count' => fn ($query) => $query->where('status', 'review'),
                'blogs as draft_count' => fn ($query) => $query->where('status', 'draft'),
            ])
            ->orderBy('name')
            ->get();

        $pendingPosts = Blog::query()
            ->pendingApproval()
            ->with('author')
            ->latest()
            ->limit(10)
            ->get();

        return [
            Heading::make('Content overview', 3),
            Grid::make([
                Column::make([
                    ValueMetric::make('Published')->value($published)->icon('check-circle'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Awaiting approval')->value($pending)->icon('clock'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Drafts')->value($drafts)->icon('pencil-square'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Authors')->value($authors)->icon('users'),
                ], 3, 12),
            ]),
            Heading::make('Authors and post counts', 3),
            Box::make([
                TableBuilder::make([
                    Text::make('Author', 'name'),
                    Email::make('Email', 'email'),
                    Text::make('Total posts', 'blogs_count'),
                    Text::make('Published', 'published_count'),
                    Text::make('Pending', 'pending_count'),
                    Text::make('Drafts', 'draft_count'),
                ], $authorRows)
                    ->cast(new ModelCaster(MoonshineUser::class))
                    ->stickyButtons(),
            ]),
            Heading::make('Posts waiting for approval', 3),
            Box::make([
                TableBuilder::make([
                    Text::make('Title', 'title'),
                    Text::make('Author', 'author_name'),
                    Date::make('Submitted', 'updated_at')->format('d M Y'),
                ], $pendingPosts)
                    ->cast(new ModelCaster(Blog::class))
                    ->buttons($this->blogRowButtons())
                    ->stickyButtons(),
            ]),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    private function authorComponents(): array
    {
        $authorId = CmsUser::id();
        $mine = Blog::query()->authoredBy((int) $authorId);

        $total = (clone $mine)->count();
        $published = (clone $mine)->where('status', 'published')->where('is_active', true)->count();
        $pending = (clone $mine)->pendingApproval()->count();
        $drafts = (clone $mine)->where('status', 'draft')->count();

        $recent = Blog::query()
            ->authoredBy((int) $authorId)
            ->latest()
            ->limit(10)
            ->get();

        return [
            Heading::make('Your writing', 3),
            Grid::make([
                Column::make([
                    ValueMetric::make('My posts')->value($total)->icon('newspaper'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Live on website')->value($published)->icon('check-circle'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Awaiting approval')->value($pending)->icon('clock'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Drafts')->value($drafts)->icon('pencil-square'),
                ], 3, 12),
            ]),
            Heading::make('Recent posts', 3),
            Box::make([
                TableBuilder::make([
                    Text::make('Title', 'title'),
                    Text::make('Status', 'status')
                        ->changePreview(static fn (mixed $value): string => match ($value) {
                            'published' => 'Approved',
                            'review' => 'Pending',
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            default => (string) $value,
                        })
                        ->badge(static fn (mixed $value): Color => match ($value) {
                            'published', 'Approved' => Color::GREEN,
                            'review', 'Pending' => Color::YELLOW,
                            'scheduled', 'Scheduled' => Color::INFO,
                            default => Color::GRAY,
                        }),
                    Date::make('Updated', 'updated_at')->format('d M Y'),
                ], $recent)
                    ->cast(new ModelCaster(Blog::class))
                    ->buttons($this->blogRowButtons())
                    ->stickyButtons(),
            ]),
        ];
    }

    /**
     * @return list<ActionButton>
     */
    private function blogRowButtons(): array
    {
        return [
            ActionButton::make(
                '',
                static function (mixed $item): string {
                    return $item instanceof Blog && filled($item->slug)
                        ? $item->previewUrl()
                        : '#';
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
                ->showInLine(),
            ActionButton::make(
                '',
                static function (mixed $item): string {
                    if (! $item instanceof Blog || blank($item->getKey())) {
                        return '#';
                    }

                    return toPage(
                        page: BlogFormPage::class,
                        resource: BlogResource::class,
                        params: ['resourceItem' => $item->getKey()],
                    );
                },
            )
                ->withoutLoading()
                ->primary()
                ->icon('pencil')
                ->class('btn-square')
                ->canSee(static function (mixed $item): bool {
                    if (! $item instanceof Blog) {
                        return false;
                    }

                    if (CmsUser::isAdmin()) {
                        return true;
                    }

                    return $item->authorCanEdit(CmsUser::id());
                })
                ->customAttributes([
                    'title' => 'Edit',
                    'aria-label' => 'Edit',
                ])
                ->showInLine(),
        ];
    }
}
