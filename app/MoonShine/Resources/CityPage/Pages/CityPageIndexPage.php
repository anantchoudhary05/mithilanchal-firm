<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CityPage\Pages;

use App\Models\CityPage;
use App\MoonShine\Resources\CityPage\CityPageResource;
use App\Support\CityPageBlueprint;
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
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<CityPageResource>
 */
final class CityPageIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            SerialNumber::forIndexPage($this),
            ID::make()->sortable()->columnSelection(hideOnInit: true),
            Text::make('City', 'city_name')->sortable(),
            Text::make('Slug', 'slug')
                ->changePreview(static fn (mixed $value): string => '/location/'.$value),
            Text::make('Status', 'status')
                ->changePreview(static fn (mixed $value): string => match ($value) {
                    CityPage::STATUS_PUBLISHED => 'Published',
                    default => 'Draft',
                })
                ->badge(static fn (mixed $value): Color => $value === CityPage::STATUS_PUBLISHED
                    ? Color::GREEN
                    : Color::GRAY),
            Text::make('Template', 'template')
                ->changePreview(static fn (mixed $value): string => CityPageBlueprint::templates()[$value] ?? (string) $value),
            Date::make('Published', 'published_at')->format('d M Y')->sortable(),
            Date::make('Updated', 'updated_at')->format('d M Y')->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Select::make('Status', 'status')->options(CityPageBlueprint::statuses()),
            Select::make('Template', 'template')->options(CityPageBlueprint::templates()),
        ];
    }

    protected function queryTags(): array
    {
        return [
            QueryTag::make(
                'Published',
                static fn (Builder $query) => $query->where('status', CityPage::STATUS_PUBLISHED),
            )->icon('check-circle'),
            QueryTag::make(
                'Drafts',
                static fn (Builder $query) => $query->where('status', CityPage::STATUS_DRAFT),
            )->icon('pencil-square'),
        ];
    }

    protected function metrics(): array
    {
        return [
            ValueMetric::make('Total')->value(CityPage::query()->count())->icon('map-pin')->columnSpan(4, 4)->class('metric-accent'),
            ValueMetric::make('Published')->value(CityPage::query()->published()->count())->icon('check-circle')->iconColor(Color::GREEN)->columnSpan(4, 4)->class('metric-accent metric-accent--ok'),
            ValueMetric::make('Drafts')->value(CityPage::query()->where('status', CityPage::STATUS_DRAFT)->count())->icon('pencil-square')->columnSpan(4, 4)->class('metric-accent metric-accent--new'),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons()
            ->prepend($this->unpublishButton())
            ->prepend($this->publishButton())
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
        return $button->customAttributes([
            'title' => 'Delete',
            'aria-label' => 'Delete',
        ]);
    }

    #[AsyncMethod]
    public function publishCity(): RedirectResponse
    {
        $page = $this->resolveCityPage();

        if ($page->isPublished()) {
            toast('This city page is already published.', ToastType::INFO);

            return back();
        }

        $page->publish();
        toast($page->city_name.' is now live in the Location menu.', ToastType::SUCCESS);

        return back();
    }

    #[AsyncMethod]
    public function unpublishCity(): RedirectResponse
    {
        $page = $this->resolveCityPage();

        if ($page->isDraft()) {
            toast('This city page is already a draft.', ToastType::INFO);

            return back();
        }

        $page->unpublish();
        toast($page->city_name.' was unpublished and removed from the Location menu.', ToastType::SUCCESS);

        return back();
    }

    private function resolveCityPage(): CityPage
    {
        $page = $this->getResource()->getItem();

        if ($page instanceof CityPage) {
            return $page;
        }

        return CityPage::query()->findOrFail((int) request()->input('resourceItem'));
    }

    private function publishButton(): ActionButton
    {
        return ActionButton::make('')
            ->method(
                method: 'publishCity',
                message: 'City page published.',
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
            ->canSee(static fn (mixed $item): bool => $item instanceof CityPage && $item->isDraft())
            ->customAttributes([
                'title' => 'Publish',
                'aria-label' => 'Publish',
            ])
            ->showInLine();
    }

    private function unpublishButton(): ActionButton
    {
        return ActionButton::make('')
            ->method(
                method: 'unpublishCity',
                message: 'City page unpublished.',
                events: [
                    $this->getListEventName(),
                    AlpineJs::event(JsEvent::FRAGMENT_UPDATED, 'crud-list'),
                ],
                page: $this,
                resource: $this->getResource(),
            )
            ->warning()
            ->icon('eye-slash')
            ->class('btn-square')
            ->canSee(static fn (mixed $item): bool => $item instanceof CityPage && $item->isPublished())
            ->customAttributes([
                'title' => 'Unpublish',
                'aria-label' => 'Unpublish',
            ])
            ->showInLine();
    }
}
