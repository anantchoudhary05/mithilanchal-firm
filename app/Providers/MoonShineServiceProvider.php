<?php

declare(strict_types=1);

namespace App\Providers;

use App\MoonShine\Resources\Blog\BlogResource;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use App\MoonShine\Resources\Homepage\HeroBannerResource;
use App\MoonShine\Resources\Homepage\OfferPageResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $core
     */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                ContactLeadResource::class,
                BlogResource::class,
                HeroBannerResource::class,
                OfferPageResource::class,
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
            ])
            ->pages([
                ...$core->getConfig()->getPages(),
            ]);
    }
}
