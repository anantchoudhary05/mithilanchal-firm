<?php

namespace App\Http\Controllers;

use App\Models\CityPage;
use App\Support\CmsUser;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function show(string $slug): View
    {
        $city = CityPage::query()
            ->published()
            ->with('sections')
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->page($city, preview: false);
    }

    public function preview(CityPage $cityPage): View
    {
        abort_unless(CmsUser::isAdmin(), 403);

        $cityPage->loadMissing('sections');
        $cityPage->ensureSections();
        $cityPage->load('sections');

        return $this->page($cityPage, preview: true);
    }

    private function page(CityPage $city, bool $preview): View
    {
        return view($city->templateView(), [
            'city' => $city,
            'isPreview' => $preview,
            'meta_title' => $preview ? 'Preview: '.$city->seoTitle() : $city->seoTitle(),
            'meta_description' => $city->meta_description,
            'meta_keywords' => $city->meta_keywords,
            'canonical_url' => $city->canonicalUrl(),
            'robots' => $preview ? 'noindex, nofollow' : 'index, follow',
            'og_title' => $city->ogTitle(),
            'og_description' => $city->ogDescription(),
            'og_image' => $city->ogImageUrl(),
        ]);
    }
}
