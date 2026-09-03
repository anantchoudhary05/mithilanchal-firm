<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Blog;
use App\Models\ContactLead;
use App\Models\MoonshineUser;
use App\MoonShine\Resources\Blog\BlogResource;
use App\MoonShine\Resources\Blog\Pages\BlogFormPage;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use App\MoonShine\Resources\ContactLead\Pages\ContactLeadFormPage;
use App\MoonShine\Resources\ContactLead\Pages\ContactLeadIndexPage;
use App\Support\CmsRole;
use App\Support\CmsUser;
use App\Support\SerialNumber;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Phone;
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

        $newLeads = ContactLead::query()->incoming()->count();
        $contactedLeads = ContactLead::query()->where('status', ContactLead::STATUS_CONTACTED)->count();
        $totalLeads = ContactLead::query()->count();
        $recentLeads = ContactLead::query()
            ->latest()
            ->limit(10)
            ->get();

        $leadIndexUrl = toPage(page: ContactLeadIndexPage::class, resource: ContactLeadResource::class);
        $blogIndexUrl = app(BlogResource::class)->getIndexPageUrl();
        $blogCreateUrl = app(BlogResource::class)->getFormPageUrl();

        return [
            $this->welcomeBanner(
                'A quick look at new enquiries and content waiting for you.',
                [
                    ['label' => 'View all leads', 'url' => $leadIndexUrl, 'class' => 'lead-chip--call'],
                    ['label' => 'Write a blog', 'url' => $blogCreateUrl, 'class' => 'lead-chip--mail'],
                ],
            ),
            $this->sectionHeading('Contact enquiries', $leadIndexUrl, 'View all'),
            Grid::make([
                Column::make([
                    ValueMetric::make('New enquiries')->value($newLeads)->icon('inbox')->iconColor(Color::YELLOW)->class('metric-accent metric-accent--new'),
                ], 4, 12),
                Column::make([
                    ValueMetric::make('Contacted')->value($contactedLeads)->icon('check-circle')->iconColor(Color::GREEN)->class('metric-accent metric-accent--ok'),
                ], 4, 12),
                Column::make([
                    ValueMetric::make('Total enquiries')->value($totalLeads)->icon('users')->class('metric-accent'),
                ], 4, 12),
            ]),
            Box::make([
                TableBuilder::make([
                    SerialNumber::field(),
                    Text::make('Name', 'name'),
                    Phone::make('Phone', 'phone')
                        ->link(
                            static function (mixed $value): string {
                                $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

                                return $digits !== '' ? 'tel:+91'.$digits : '#';
                            },
                            static fn (mixed $value): string => filled($value) ? (string) $value : '—',
                            icon: 'phone',
                        ),
                    Text::make('Requirement', 'requirement'),
                    Text::make('Status', 'status')
                        ->changePreview(static fn (mixed $value): string => ContactLead::statusOptions()[(string) $value] ?? (string) $value)
                        ->badge(static fn (mixed $value): Color => match ($value) {
                            ContactLead::STATUS_NEW, 'New' => Color::YELLOW,
                            ContactLead::STATUS_CONTACTED, 'Contacted' => Color::GREEN,
                            default => Color::GRAY,
                        }),
                    Date::make('Received', 'created_at')->format('d M Y H:i'),
                ], $recentLeads)
                    ->cast(new ModelCaster(ContactLead::class))
                    ->buttons($this->leadRowButtons())
                    ->stickyButtons()
                    ->withNotFound(),
            ]),
            $this->sectionHeading('Content overview', $blogIndexUrl, 'View blogs'),
            Grid::make([
                Column::make([
                    ValueMetric::make('Published')->value($published)->icon('check-circle')->iconColor(Color::GREEN)->class('metric-accent metric-accent--ok'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Awaiting approval')->value($pending)->icon('clock')->iconColor(Color::YELLOW)->class('metric-accent metric-accent--new'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Drafts')->value($drafts)->icon('pencil-square')->class('metric-accent'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Authors')->value($authors)->icon('users')->class('metric-accent'),
                ], 3, 12),
            ]),
            Grid::make([
                Column::make([
                    Heading::make('Authors and post counts', 4),
                    Box::make([
                        TableBuilder::make([
                            SerialNumber::field(),
                            Text::make('Author', 'name'),
                            Text::make('Published', 'published_count'),
                            Text::make('Pending', 'pending_count'),
                            Text::make('Drafts', 'draft_count'),
                        ], $authorRows)
                            ->cast(new ModelCaster(MoonshineUser::class))
                            ->withNotFound(),
                    ]),
                ], 6, 12),
                Column::make([
                    Heading::make('Posts waiting for approval', 4),
                    Box::make([
                        TableBuilder::make([
                            SerialNumber::field(),
                            Text::make('Title', 'title'),
                            Text::make('Author', 'author_name'),
                            Date::make('Submitted', 'updated_at')->format('d M Y'),
                        ], $pendingPosts)
                            ->cast(new ModelCaster(Blog::class))
                            ->buttons($this->blogRowButtons())
                            ->stickyButtons()
                            ->withNotFound(),
                    ]),
                ], 6, 12),
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

        $blogIndexUrl = app(BlogResource::class)->getIndexPageUrl();
        $blogCreateUrl = app(BlogResource::class)->getFormPageUrl();

        return [
            $this->welcomeBanner(
                'Draft, preview, and submit posts for approval from here.',
                [
                    ['label' => 'My blogs', 'url' => $blogIndexUrl, 'class' => 'lead-chip--call'],
                    ['label' => 'Write a post', 'url' => $blogCreateUrl, 'class' => 'lead-chip--mail'],
                ],
            ),
            Heading::make('Your writing', 3),
            Grid::make([
                Column::make([
                    ValueMetric::make('My posts')->value($total)->icon('newspaper')->class('metric-accent'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Live on website')->value($published)->icon('check-circle')->iconColor(Color::GREEN)->class('metric-accent metric-accent--ok'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Awaiting approval')->value($pending)->icon('clock')->iconColor(Color::YELLOW)->class('metric-accent metric-accent--new'),
                ], 3, 12),
                Column::make([
                    ValueMetric::make('Drafts')->value($drafts)->icon('pencil-square')->class('metric-accent'),
                ], 3, 12),
            ]),
            $this->sectionHeading('Recent posts', $blogIndexUrl, 'View all'),
            Box::make([
                TableBuilder::make([
                    SerialNumber::field(),
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
                    ->stickyButtons()
                    ->withNotFound(),
            ]),
        ];
    }

    /**
     * @param  list<array{label: string, url: string, class?: string}>  $actions
     */
    private function welcomeBanner(string $subtitle, array $actions): FlexibleRender
    {
        return FlexibleRender::make(
            view('moonshine.dashboard-welcome', [
                'name' => CmsUser::user()?->name ?: (CmsUser::isAuthor() ? 'Author' : 'Admin'),
                'subtitle' => $subtitle,
                'actions' => $actions,
            ]),
        );
    }

    private function sectionHeading(string $title, string $url, string $label): Flex
    {
        return Flex::make([
            Heading::make($title, 3),
            Link::make($url, $label)->button()->icon('arrow-right'),
        ])->justifyAlign('between')->itemsAlign('center');
    }

    /**
     * @return list<ActionButton>
     */
    private function leadRowButtons(): array
    {
        return [
            ActionButton::make(
                '',
                static function (mixed $item): string {
                    if (! $item instanceof ContactLead || blank($item->getKey())) {
                        return '#';
                    }

                    return toPage(
                        page: ContactLeadFormPage::class,
                        resource: ContactLeadResource::class,
                        params: ['resourceItem' => $item->getKey()],
                    );
                },
            )
                ->withoutLoading()
                ->primary()
                ->icon('eye')
                ->class('btn-square')
                ->canSee(static fn (mixed $item): bool => $item instanceof ContactLead && CmsUser::isAdmin())
                ->customAttributes([
                    'title' => 'View enquiry',
                    'aria-label' => 'View enquiry',
                ])
                ->showInLine(),
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
