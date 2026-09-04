<?php

namespace Database\Seeders;

use App\Models\CityPage;
use App\Support\CityPageBlueprint;
use Illuminate\Database\Seeder;

class CityPageSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'city_name' => 'Patna',
                'state' => 'Bihar',
                'slug' => 'patna',
                'template' => CityPageBlueprint::TEMPLATE_STANDARD,
            ],
            [
                'city_name' => 'Darbhanga',
                'state' => 'Bihar',
                'slug' => 'darbhanga',
                'template' => CityPageBlueprint::TEMPLATE_MODERN,
            ],
            [
                'city_name' => 'Muzaffarpur',
                'state' => 'Bihar',
                'slug' => 'muzaffarpur',
                'template' => CityPageBlueprint::TEMPLATE_MINIMAL,
            ],
        ];

        foreach ($cities as $city) {
            $page = CityPage::query()->updateOrCreate(
                ['slug' => $city['slug']],
                [
                    'city_name' => $city['city_name'],
                    'state' => $city['state'],
                    'template' => $city['template'],
                    'status' => CityPage::STATUS_PUBLISHED,
                    'seo_title' => 'Premium '.$city['city_name'].' Makhana Supplier | Mithilanchal Farms',
                    'meta_description' => 'Buy premium '.$city['city_name'].' makhana in bulk from Mithilanchal Farms.',
                    'meta_keywords' => $city['city_name'].' makhana, makhana supplier '.$city['city_name'],
                    'published_at' => now()->subDay(),
                ],
            );

            $page->ensureSections();
        }

        CityPage::clearCaches();
    }
}
