<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Pages\ProfilePage;
use App\MoonShine\Resources\Blog\BlogResource;
use App\MoonShine\Resources\CityPage\CityPageResource;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use App\MoonShine\Resources\Homepage\HeroBannerResource;
use App\MoonShine\Resources\Homepage\OfferPageResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\Support\CmsUser;
use MoonShine\AssetManager\InlineCss;
use MoonShine\ColorManager\ColorManager;
use MoonShine\ColorManager\Palettes\GreenPalette;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\Laravel\Components\Layout\Profile;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = GreenPalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make(
                <<<'CSS'
.table-list td.sticky-col {
    white-space: nowrap;
    background: var(--ms-cm-body, #fff);
}
.table-list td.sticky-col .flex {
    flex-wrap: nowrap;
    gap: 0.25rem;
}
.cms-welcome {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem 1.5rem;
    margin-bottom: 1.25rem;
    padding: 1.15rem 1.35rem;
    border: 1px solid color-mix(in oklab, var(--color-primary, #175c20) 18%, transparent);
    border-radius: 1rem;
    background:
        linear-gradient(135deg, color-mix(in oklab, var(--color-primary, #175c20) 10%, #fff) 0%, #fff 58%),
        #fff;
}
.cms-welcome__eyebrow {
    margin: 0 0 0.2rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: color-mix(in oklab, var(--color-base-text, #1c241c) 62%, transparent);
}
.cms-welcome__title {
    margin: 0;
    font-size: 1.45rem;
    line-height: 1.25;
}
.cms-welcome__copy {
    margin: 0.3rem 0 0;
    color: color-mix(in oklab, var(--color-base-text, #1c241c) 70%, transparent);
}
.cms-welcome__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.metric-accent .report-card {
    position: relative;
    overflow: hidden;
}
.metric-accent .report-card::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 4px;
    background: var(--color-primary, #175c20);
}
.metric-accent--new .report-card::before { background: #c99a35; }
.metric-accent--ok .report-card::before { background: #2f9e44; }
.lead-detail-grid { align-items: start; }
.lead-followup-col { align-self: start; }
@media (min-width: 1024px) {
    .lead-followup-col { position: sticky; top: 5.5rem; }
}
.lead-summary {
    overflow: hidden;
    border: 1px solid color-mix(in oklab, var(--color-primary, #175c20) 16%, #e5e7eb);
    border-radius: 1rem;
    background: #fff;
}
.lead-summary__header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem 1rem;
    padding: 1.15rem 1.35rem 1rem;
    background: linear-gradient(135deg, rgba(23, 92, 32, 0.08), rgba(201, 154, 53, 0.1));
    border-bottom: 1px solid color-mix(in oklab, var(--color-primary, #175c20) 12%, #e5e7eb);
}
.lead-summary__eyebrow {
    margin: 0 0 0.15rem;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #5f6b62;
}
.lead-summary__name {
    margin: 0;
    font-size: 1.35rem;
    line-height: 1.2;
}
.lead-summary__meta {
    margin: 0.25rem 0 0;
    color: #5f6b62;
    font-size: 0.9rem;
}
.lead-summary__status {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.7rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    background: #eef2f0;
    color: #33403a;
}
.lead-summary__status--new { background: #fff4d6; color: #8a5a00; }
.lead-summary__status--contacted { background: #e5f8ea; color: #146c2e; }
.lead-summary__status--closed { background: #eef0ee; color: #4b5563; }
.lead-summary__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.85rem 1.35rem;
    border-bottom: 1px solid #edf1ee;
}
.lead-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2.15rem;
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 700;
    text-decoration: none !important;
    border: 1px solid transparent;
}
.lead-chip--call { background: #175c20; color: #fff !important; }
.lead-chip--wa { background: #128c7e; color: #fff !important; }
.lead-chip--mail { background: #fff; color: #175c20 !important; border-color: #cfe0d2; }
.lead-summary__grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin: 0;
}
.lead-summary__cell {
    padding: 0.9rem 1.35rem;
    border-bottom: 1px solid #edf1ee;
    border-right: 1px solid #edf1ee;
}
.lead-summary__cell:nth-child(2n) { border-right: 0; }
.lead-summary__cell--wide { grid-column: 1 / -1; border-right: 0; }
.lead-summary__cell dt {
    margin: 0 0 0.2rem;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #6b7280;
}
.lead-summary__cell dd { margin: 0; font-weight: 600; }
.lead-summary__message { font-weight: 500; white-space: pre-wrap; line-height: 1.55; }
@media (max-width: 767px) {
    .lead-summary__grid { grid-template-columns: 1fr; }
    .lead-summary__cell { border-right: 0; }
}
CSS
            ),
        ];
    }

    /**
     * Show name + a visible logout button instead of an avatar-only dropdown.
     */
    protected function getProfileComponent(): Profile
    {
        return Profile::make();
    }

    protected function menu(): array
    {
        return [
            MenuItem::make(
                static fn (): string => moonshineRouter()->getEndpoints()->home(),
                'Dashboard',
                'home',
            ),
            MenuGroup::make('Leads', [
                MenuItem::make(ContactLeadResource::class, 'Contact enquiries'),
            ])->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),
            MenuGroup::make('Content', [
                MenuItem::make(BlogResource::class, CmsUser::isAuthor() ? 'My Blogs' : 'Blogs'),
                MenuItem::make(CityPageResource::class, 'Location / City Pages')
                    ->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),
            ]),
            MenuGroup::make('Homepage', [
                MenuItem::make(HeroBannerResource::class, 'Banner pages'),
                MenuItem::make(OfferPageResource::class, 'Offer pages'),
            ])->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),
            MenuGroup::make('People', [
                MenuItem::make(MoonShineUserResource::class, 'Authors'),
                MenuItem::make(MoonShineUserRoleResource::class, 'Roles'),
            ])->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),
            MenuItem::make(
                static fn (): string => toPage(page: ProfilePage::class),
                'My profile',
                'user',
            ),
            MenuItem::make(
                static fn (): string => route('cms.logout'),
                'Log out',
                'arrow-right-start-on-rectangle',
            ),
        ];
    }

    /**
     * @param  ColorManager  $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);
    }
}
