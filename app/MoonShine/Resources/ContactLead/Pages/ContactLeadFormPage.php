<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ContactLead\Pages;

use App\Models\ContactLead;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<ContactLeadResource, ContactLead>
 */
final class ContactLeadFormPage extends FormPage
{
    public function getTitle(): string
    {
        $lead = $this->getItem();

        if ($lead instanceof ContactLead && filled($lead->name)) {
            return $lead->name;
        }

        return 'Enquiry';
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $lead = $this->getItem();

        return [
            Grid::make([
                Column::make([
                    FlexibleRender::make(
                        view('moonshine.contact-lead-summary', [
                            'lead' => $lead instanceof ContactLead ? $lead : new ContactLead,
                        ]),
                    ),
                ], 8, 12),
                Column::make([
                    Box::make('Follow-up', [
                        Select::make('Status', 'status')
                            ->options(ContactLead::statusOptions())
                            ->required()
                            ->hint('New when it arrives. Contacted after you call or WhatsApp. Closed when the enquiry is done.'),
                        Textarea::make('Admin notes', 'admin_notes')
                            ->hint('Internal notes for the team. Not shown to the customer.'),
                    ])->icon('clipboard-document-check'),
                ], 4, 12)->class('lead-followup-col'),
            ])->class('lead-detail-grid'),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons()
            ->prepend($this->emailButton())
            ->prepend($this->whatsappButton())
            ->prepend($this->callButton());
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'status' => ['required', 'in:new,contacted,closed'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Delete', 'aria-label' => 'Delete enquiry']);
    }

    private function callButton(): ActionButton
    {
        return ActionButton::make(
            'Call',
            static function (mixed $item): string {
                return $item instanceof ContactLead ? ($item->telHref() ?? '#') : '#';
            },
        )
            ->withoutLoading()
            ->success()
            ->icon('phone')
            ->canSee(static fn (mixed $item): bool => $item instanceof ContactLead && filled($item->telHref()))
            ->customAttributes([
                'title' => 'Call this enquiry',
                'aria-label' => 'Call this enquiry',
            ])
            ->showInLine();
    }

    private function whatsappButton(): ActionButton
    {
        return ActionButton::make(
            'WhatsApp',
            static function (mixed $item): string {
                return $item instanceof ContactLead ? ($item->whatsappHref() ?? '#') : '#';
            },
        )
            ->blank()
            ->withoutLoading()
            ->info()
            ->icon('chat-bubble-left-right')
            ->canSee(static fn (mixed $item): bool => $item instanceof ContactLead && filled($item->whatsappHref()))
            ->customAttributes([
                'title' => 'Open WhatsApp',
                'aria-label' => 'Open WhatsApp',
            ])
            ->showInLine();
    }

    private function emailButton(): ActionButton
    {
        return ActionButton::make(
            'Email',
            static function (mixed $item): string {
                return $item instanceof ContactLead ? ($item->mailtoHref() ?? '#') : '#';
            },
        )
            ->withoutLoading()
            ->primary()
            ->icon('envelope')
            ->canSee(static fn (mixed $item): bool => $item instanceof ContactLead && filled($item->mailtoHref()))
            ->customAttributes([
                'title' => 'Email this enquiry',
                'aria-label' => 'Email this enquiry',
            ])
            ->showInLine();
    }
}
