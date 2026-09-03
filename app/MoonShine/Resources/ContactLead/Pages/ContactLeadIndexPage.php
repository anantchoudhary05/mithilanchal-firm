<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ContactLead\Pages;

use App\Models\ContactLead;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use App\Support\CmsUser;
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
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ContactLeadResource>
 */
final class ContactLeadIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable()->columnSelection(hideOnInit: true),
            Text::make('Name', 'name')->sortable(),
            Text::make('Company', 'company'),
            Email::make('Email', 'email'),
            Phone::make('Phone', 'phone'),
            Text::make('Requirement', 'requirement'),
            Text::make('Status', 'status')
                ->changePreview(static fn (mixed $value): string => ContactLead::statusOptions()[(string) $value] ?? (string) $value)
                ->badge(static fn (mixed $value): Color => match ($value) {
                    ContactLead::STATUS_NEW, 'New' => Color::YELLOW,
                    ContactLead::STATUS_CONTACTED, 'Contacted' => Color::GREEN,
                    ContactLead::STATUS_CLOSED, 'Closed' => Color::GRAY,
                    default => Color::GRAY,
                }),
            Date::make('Received', 'created_at')->format('d M Y H:i')->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Select::make('Status', 'status')->options(ContactLead::statusOptions()),
            Select::make('Requirement', 'requirement')->options(
                array_combine(ContactLead::REQUIREMENTS, ContactLead::REQUIREMENTS)
            ),
        ];
    }

    protected function queryTags(): array
    {
        return [
            QueryTag::make(
                'New',
                static fn (Builder $query) => $query->where('status', ContactLead::STATUS_NEW),
            )->icon('inbox'),
            QueryTag::make(
                'Contacted',
                static fn (Builder $query) => $query->where('status', ContactLead::STATUS_CONTACTED),
            )->icon('check-circle'),
            QueryTag::make(
                'Closed',
                static fn (Builder $query) => $query->where('status', ContactLead::STATUS_CLOSED),
            )->icon('archive-box'),
        ];
    }

    protected function metrics(): array
    {
        $query = ContactLead::query();

        return [
            ValueMetric::make('Total')->value((clone $query)->count())->icon('inbox')->columnSpan(3, 3),
            ValueMetric::make('New')->value((clone $query)->incoming()->count())->icon('bell')->columnSpan(3, 3),
            ValueMetric::make('Contacted')->value((clone $query)->where('status', ContactLead::STATUS_CONTACTED)->count())->icon('check-circle')->columnSpan(3, 3),
            ValueMetric::make('Closed')->value((clone $query)->where('status', ContactLead::STATUS_CLOSED)->count())->icon('archive-box')->columnSpan(3, 3),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons()->prepend($this->markContactedButton());
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
        return $button->customAttributes(['title' => 'View', 'aria-label' => 'View enquiry']);
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Delete', 'aria-label' => 'Delete enquiry']);
    }

    #[AsyncMethod]
    public function markContacted(): RedirectResponse
    {
        if (! CmsUser::isAdmin()) {
            abort(403, 'Only an admin can update enquiries.');
        }

        $lead = $this->getResource()->getItem();

        if (! $lead instanceof ContactLead) {
            $lead = ContactLead::query()->findOrFail((int) request()->input('resourceItem'));
        }

        if (! $lead->isNew()) {
            toast('Only new enquiries can be marked as contacted.', ToastType::ERROR);

            return back();
        }

        $lead->markContacted();

        toast('Enquiry marked as contacted.', ToastType::SUCCESS);

        return back();
    }

    private function markContactedButton(): ActionButton
    {
        return ActionButton::make('')
            ->method(
                method: 'markContacted',
                message: 'Enquiry marked as contacted.',
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
                && $item instanceof ContactLead
                && $item->isNew())
            ->customAttributes([
                'title' => 'Mark as contacted',
                'aria-label' => 'Mark as contacted',
            ])
            ->showInLine();
    }
}
