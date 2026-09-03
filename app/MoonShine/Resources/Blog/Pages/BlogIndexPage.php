<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Blog\Pages;

use App\Models\Blog;
use App\MoonShine\Resources\Blog\BlogResource;
use App\Support\CmsUser;
use App\Support\SerialNumber;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\QueryTags\QueryTag;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\ToastType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<BlogResource>
 */
final class BlogIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        $adminOnly = static fn (mixed $item): bool => CmsUser::isAdmin();

        return [
            SerialNumber::forIndexPage($this),
            ID::make()->sortable()->columnSelection(hideOnInit: true),
            Image::make('Image', 'featured_image')
                ->disk('public')
                ->dir('blogs')
                ->modifyRawValue(fn (?string $raw): string => $raw ?? ''),
            Text::make('Title', 'title')->sortable(),
            Text::make('Author', 'author_name')
                ->canSee($adminOnly),
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
            Switcher::make('Active', 'is_active')
                ->updateOnPreview()
                ->canSee($adminOnly),
            Switcher::make('Featured', 'is_featured')
                ->updateOnPreview()
                ->canSee($adminOnly)
                ->columnSelection(hideOnInit: true),
            Date::make('Published', 'published_at')->format('d M Y')->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Select::make('Status', 'status')->options($this->statusOptions()),
            Select::make('Content Type', 'content_type')->options([
                'guide' => 'Guide',
                'news' => 'News',
                'case_study' => 'Case Study',
                'technical' => 'Technical',
                'comparison' => 'Comparison',
                'product_education' => 'Product Education',
            ]),
            Switcher::make('Active', 'is_active'),
            Switcher::make('Featured', 'is_featured'),
        ];
    }

    protected function queryTags(): array
    {
        return [
            QueryTag::make(
                'Awaiting approval',
                static fn (Builder $query) => $query->where('status', 'review'),
            )->icon('clock'),
            QueryTag::make(
                'Approved',
                static fn (Builder $query) => $query->where('status', 'published')->where('is_active', true),
            )->icon('check-circle'),
            QueryTag::make(
                'Drafts',
                static fn (Builder $query) => $query->where('status', 'draft'),
            )->icon('pencil-square'),
        ];
    }

    protected function metrics(): array
    {
        $query = Blog::query();

        if (CmsUser::isAuthor() && CmsUser::id() !== null) {
            $query->where('author_id', CmsUser::id());
        }

        return [
            ValueMetric::make('Total')->value((clone $query)->count())->icon('newspaper')->columnSpan(3, 3)->class('metric-accent'),
            ValueMetric::make('Live')->value((clone $query)->where('status', 'published')->where('is_active', true)->count())->icon('check-circle')->iconColor(Color::GREEN)->columnSpan(3, 3)->class('metric-accent metric-accent--ok'),
            ValueMetric::make('Pending')->value((clone $query)->where('status', 'review')->count())->icon('clock')->iconColor(Color::YELLOW)->columnSpan(3, 3)->class('metric-accent metric-accent--new'),
            ValueMetric::make('Drafts')->value((clone $query)->where('status', 'draft')->count())->icon('pencil-square')->columnSpan(3, 3)->class('metric-accent'),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons()
            ->prepend($this->approveButton())
            ->prepend($this->getResource()->getPreviewButton());
    }

    /**
     * @param  TableBuilder  $component
     */
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->stickyButtons()->columnSelection();
    }

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Edit', 'aria-label' => 'Edit']);
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Delete', 'aria-label' => 'Delete']);
    }

    #[AsyncMethod]
    public function approve(): RedirectResponse
    {
        if (! CmsUser::isAdmin()) {
            abort(403, 'Only an admin can approve blog posts.');
        }

        $blog = $this->getResource()->getItem();

        if (! $blog instanceof Blog) {
            $blog = Blog::query()->findOrFail((int) request()->input('resourceItem'));
        }

        if (! $blog->isAwaitingApproval()) {
            toast('Only posts awaiting approval can be approved.', ToastType::ERROR);

            return back();
        }

        $blog->approve();

        toast('Post approved and published on the website.', ToastType::SUCCESS);

        return back();
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'review' => 'Awaiting approval',
            'scheduled' => 'Scheduled',
            'published' => 'Approved',
        ];
    }

    private function approveButton(): ActionButton
    {
        return ActionButton::make('')
            ->method(
                method: 'approve',
                message: 'Post approved and published on the website.',
                events: [
                    $this->getListEventName(),
                    AlpineJs::event(JsEvent::FRAGMENT_UPDATED, 'crud-list'),
                ],
                page: $this,
                resource: $this->getResource(),
            )
            ->success()
            ->icon('check')
            ->class('btn-square')
            ->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()
                && $item instanceof Blog
                && $item->isAwaitingApproval())
            ->customAttributes([
                'title' => 'Approve and publish',
                'aria-label' => 'Approve and publish',
            ])
            ->showInLine();
    }
}
