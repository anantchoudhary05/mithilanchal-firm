<?php

declare(strict_types=1);

namespace App\MoonShine\Handlers;

use App\Exports\ContactLeadExcelExporter;
use App\Support\CmsUser;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\UI\Components\ActionButton;
use Symfony\Component\HttpFoundation\Response;

final class ContactLeadExcelExportHandler extends Handler
{
    protected ?string $alias = 'excel-export';

    public function handle(): Response
    {
        abort_unless(CmsUser::isAdmin(), 403, 'Only an admin can export enquiries.');

        return (new ContactLeadExcelExporter)->download();
    }

    public function getButton(): ActionButtonContract
    {
        return $this->prepareButton(
            ActionButton::make($this->getLabel(), $this->getUrl())
                ->withoutLoading()
                ->primary()
                ->icon('arrow-down-tray')
                ->canSee(static fn (mixed ...$unused): bool => CmsUser::isAdmin())
                ->customAttributes([
                    'title' => 'Download New, Contacted and Closed sheets',
                    'aria-label' => 'Export Excel report',
                ]),
        );
    }
}
