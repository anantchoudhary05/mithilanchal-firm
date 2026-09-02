<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Pages\ProfilePage;
use App\MoonShine\Resources\Blog\BlogResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\Support\CmsUser;
use MoonShine\AssetManager\InlineCss;
use MoonShine\ColorManager\ColorManager;
use MoonShine\ColorManager\Palettes\PurplePalette;
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
    protected ?string $palette = PurplePalette::class;

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
            MenuGroup::make('Content', [
                MenuItem::make(BlogResource::class, CmsUser::isAuthor() ? 'My Blogs' : 'Blogs'),
            ]),
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
