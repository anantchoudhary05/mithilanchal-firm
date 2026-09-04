<?php

namespace Database\Factories;

use App\Models\CityPage;
use App\Support\CityPageBlueprint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CityPage>
 */
class CityPageFactory extends Factory
{
    protected $model = CityPage::class;

    public function definition(): array
    {
        $city = fake()->unique()->city();

        return [
            'city_name' => $city,
            'state' => 'Bihar',
            'slug' => Str::slug($city),
            'template' => CityPageBlueprint::TEMPLATE_STANDARD,
            'status' => CityPage::STATUS_PUBLISHED,
            'seo_title' => 'Premium '.$city.' Makhana Supplier | Mithilanchal Farms',
            'meta_description' => 'Buy premium quality '.$city.' makhana directly from Mallah Makhana for wholesale and bulk orders.',
            'meta_keywords' => $city.' makhana, makhana supplier '.$city.', fox nuts '.$city,
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => CityPage::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function modern(): static
    {
        return $this->state(fn (): array => [
            'template' => CityPageBlueprint::TEMPLATE_MODERN,
        ]);
    }

    public function minimal(): static
    {
        return $this->state(fn (): array => [
            'template' => CityPageBlueprint::TEMPLATE_MINIMAL,
        ]);
    }
}
