<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ContactLead\Pages;

use App\Models\ContactLead;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<ContactLeadResource, ContactLead>
 */
final class ContactLeadFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Enquiry', [
                ID::make(),
                Preview::make('Full name', 'name'),
                Preview::make('Company', 'company'),
                Preview::make('Email', 'email'),
                Preview::make('Phone', 'phone'),
                Preview::make('Requirement', 'requirement'),
                Preview::make('Estimated quantity', 'quantity'),
                Preview::make('Message', 'message'),
                Preview::make('Received', 'created_at')
                    ->changePreview(static function (mixed $value): string {
                        return $value instanceof \DateTimeInterface
                            ? $value->format('d M Y H:i')
                            : (string) $value;
                    }),
            ]),
            Box::make('Follow-up', [
                Select::make('Status', 'status')
                    ->options(ContactLead::statusOptions())
                    ->required(),
                Textarea::make('Admin notes', 'admin_notes')
                    ->hint('Internal notes for the team. Not shown to the customer.'),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'status' => ['required', 'in:new,contacted,closed'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
