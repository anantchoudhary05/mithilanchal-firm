<?php

namespace Database\Factories;

use App\Models\HomepageSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomepageSection>
 */
class HomepageSectionFactory extends Factory
{
    protected $model = HomepageSection::class;

    public function definition(): array
    {
        return [
            'type' => HomepageSection::TYPE_HERO_BANNER,
            'name' => fake()->unique()->words(3, true),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 90),
            'show_as_popup' => false,
            'eyebrow' => 'FROM MITHILA\'S PONDS',
            'headline' => 'Rooted in Mithilanchal.',
            'headline_highlight' => 'Grown with care.',
            'description' => 'Premium fox nuts from Darbhanga, popped by local hands and graded with honesty.',
            'background_image' => 'assests/img/hq-roasted.jpg',
            'button_text' => 'Explore Products',
            'button_url' => '/product',
            'button_2_text' => 'Discover Our Story',
            'button_2_url' => '/our-story',
            'features' => null,
            'payload' => null,
        ];
    }

    public function offer(): static
    {
        return $this->state(fn (): array => [
            'type' => HomepageSection::TYPE_OFFER,
            'name' => 'Welcome offer',
            'show_as_popup' => true,
            'eyebrow' => null,
            'headline' => 'Special Welcome Offer!',
            'headline_highlight' => null,
            'description' => null,
            'background_image' => 'assests/img/hq-roast-bihar.jpg',
            'button_text' => 'SHOP NOW & SAVE',
            'button_url' => '/product',
            'button_2_text' => null,
            'button_2_url' => null,
            'tagline' => 'Goodness in every bite!',
            'discount_text' => '15% OFF',
            'discount_subtext' => 'ON YOUR FIRST ORDER',
            'coupon_code' => 'MAKHANA15',
            'product_image' => 'assests/img/hq-white.jpg',
            'bowl_image' => 'assests/img/hq-bowl.jpg',
            'badge_text' => 'HEALTHY TASTY WHOLESOME',
            'shipping_text' => 'Free Shipping On Orders Above ₹499',
            'urgency_text' => 'Hurry! Offer valid for a limited time only.',
            'social_proof' => 'Loved by 10,000+ Happy Customers',
            'features' => HomepageSection::defaultFeatures(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
