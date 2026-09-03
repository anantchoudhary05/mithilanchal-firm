<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ContactLead\Pages;

use App\Models\ContactLead;
use App\MoonShine\Handlers\ContactLeadExcelExportHandler;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use App\Support\CmsUser;
use App\Support\SerialNumber;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Handlers\Handler;
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
            SerialNumber::forIndexPage($this),
            ID::make()->sortable()->columnSelection(hideOnInit: true),
            Text::make('Name', 'name')->sortable(),
            Text::make('Company', 'company')->columnSelection(hideOnInit: true),
            Email::make('Email', 'email')
                ->link(
                    static fn (mixed $value): string => filled($value) ? 'mailto:'.$value : '#',
                    static fn (mixed $value): string => filled($value) ? (string) $value : '—',
                    icon: 'envelope',
                ),
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
            ValueMetric::make('Total')->value((clone $query)->count())->icon('inbox')->columnSpan(3, 3)->class('metric-accent'),
            ValueMetric::make('New')->value((clone $query)->incoming()->count())->icon('bell')->iconColor(Color::YELLOW)->columnSpan(3, 3)->class('metric-accent metric-accent--new'),
            ValueMetric::make('Contacted')->value((clone $query)->where('status', ContactLead::STATUS_CONTACTED)->count())->icon('check-circle')->iconColor(Color::GREEN)->columnSpan(3, 3)->class('metric-accent metric-accent--ok'),
            ValueMetric::make('Closed')->value((clone $query)->where('status', ContactLead::STATUS_CLOSED)->count())->icon('archive-box')->columnSpan(3, 3)->class('metric-accent'),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons()
            ->prepend($this->whatsappButton())
            ->prepend($this->callButton())
            ->prepend($this->markContactedButton());
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

    private function callButton(): ActionButton
    {
        return ActionButton::make(
            '',
            static function (mixed $item): string {
                return $item instanceof ContactLead ? ($item->telHref() ?? '#') : '#';
            },
        )
            ->withoutLoading()
            ->success()
            ->icon('phone')
            ->class('btn-square')
            ->canSee(static fn (mixed $item): bool => $item instanceof ContactLead && filled($item->telHref()))
            ->customAttributes([
                'title' => 'Call',
                'aria-label' => 'Call',
            ])
            ->showInLine();
    }

    private function whatsappButton(): ActionButton
    {
        return ActionButton::make(
            '',
            static function (mixed $item): string {
                return $item instanceof ContactLead ? ($item->whatsappHref() ?? '#') : '#';
            },
        )
            ->blank()
            ->withoutLoading()
            ->info()
            ->icon('chat-bubble-left-right')
            ->class('btn-square')
            ->canSee(static fn (mixed $item): bool => $item instanceof ContactLead && filled($item->whatsappHref()))
            ->customAttributes([
                'title' => 'WhatsApp',
                'aria-label' => 'WhatsApp',
            ])
            ->showInLine();
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

    /**
     * @return ListOf<Handler>
     */
    protected function handlers(): ListOf
    {
        return new ListOf(Handler::class, [
            ContactLeadExcelExportHandler::make('Export Excel')
                ->icon('arrow-down-tray'),
        ]);
    }
}
