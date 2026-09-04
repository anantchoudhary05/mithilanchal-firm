<?php

namespace App\Providers;

use App\Models\CityPage;
use App\Models\HomepageSection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer(['components.layout', 'components.header'], function ($view): void {
            if ($view->name() === 'components.layout') {
                $view->with(
                    'popupOffer',
                    Schema::hasTable('homepage_sections') ? HomepageSection::activePopupOffer() : null,
                );
            }

            $view->with('locationCities', CityPage::navCities());
        });
    }
}
