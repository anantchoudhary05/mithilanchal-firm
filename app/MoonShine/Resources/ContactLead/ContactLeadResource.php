<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ContactLead;

use App\Models\ContactLead;
use App\MoonShine\Resources\ContactLead\Pages\ContactLeadFormPage;
use App\MoonShine\Resources\ContactLead\Pages\ContactLeadIndexPage;
use App\Support\CmsUser;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<ContactLead, ContactLeadIndexPage, ContactLeadFormPage, null>
 */
#[Icon('inbox')]
#[Group('Leads')]
#[Order(0)]
class ContactLeadResource extends ModelResource
{
    protected string $model = ContactLead::class;

    protected string $column = 'name';

    protected string $sortColumn = 'created_at';

    protected SortDirection $sortDirection = SortDirection::DESC;

    public function getTitle(): string
    {
        return 'Contact enquiries';
    }

    public function canSee(): bool
    {
        return CmsUser::isAdmin();
    }

    protected function isCan(Ability $ability): bool
    {
        if (! CmsUser::isAdmin()) {
            return false;
        }

        return match ($ability) {
            Ability::CREATE => false,
            default => true,
        };
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::CREATE, Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            ContactLeadIndexPage::class,
            ContactLeadFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'company',
            'email',
            'phone',
            'requirement',
            'message',
        ];
    }
}
