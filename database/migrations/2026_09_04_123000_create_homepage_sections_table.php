<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('show_as_popup')->default(false);
            $table->string('eyebrow')->nullable();
            $table->string('headline')->nullable();
            $table->string('headline_highlight')->nullable();
            $table->text('description')->nullable();
            $table->string('background_image')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('button_2_text')->nullable();
            $table->string('button_2_url')->nullable();
            $table->string('tagline')->nullable();
            $table->string('discount_text')->nullable();
            $table->string('discount_subtext')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('product_image')->nullable();
            $table->string('bowl_image')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('shipping_text')->nullable();
            $table->string('urgency_text')->nullable();
            $table->string('social_proof')->nullable();
            $table->json('features')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active', 'sort_order']);
        });

        $now = now();

        DB::table('homepage_sections')->insert([
            [
                'type' => 'hero_banner',
                'name' => 'Welcome hero',
                'is_active' => true,
                'sort_order' => 10,
                'show_as_popup' => false,
                'eyebrow' => "FROM MITHILA'S PONDS",
                'headline' => 'Rooted in Mithilanchal.',
                'headline_highlight' => 'Grown with care.',
                'description' => 'Premium fox nuts from Darbhanga — popped by local hands, graded with honesty, and shared with homes and businesses that value the real taste of Bihar.',
                'background_image' => 'assests/img/hq-roasted.jpg',
                'button_text' => 'Discover Our Story',
                'button_url' => '/our-story',
                'button_2_text' => 'Explore Products',
                'button_2_url' => '/product',
                'tagline' => null,
                'discount_text' => null,
                'discount_subtext' => null,
                'coupon_code' => null,
                'product_image' => null,
                'bowl_image' => null,
                'badge_text' => null,
                'shipping_text' => null,
                'urgency_text' => null,
                'social_proof' => null,
                'features' => null,
                'payload' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'offer',
                'name' => 'Welcome offer',
                'is_active' => true,
                'sort_order' => 20,
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
                'features' => json_encode([
                    ['icon' => 'leaf', 'text' => '100% Natural & Premium'],
                    ['icon' => 'seedling', 'text' => 'Rich in Protein & Fiber'],
                    ['icon' => 'heart', 'text' => 'Healthy Snacking Made Delicious'],
                ]),
                'payload' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'hero_banner',
                'name' => 'Harvest slide',
                'is_active' => true,
                'sort_order' => 30,
                'show_as_popup' => false,
                'eyebrow' => 'FROM DARBHANGA PONDS',
                'headline' => 'Popped by local hands.',
                'headline_highlight' => 'Graded with honesty.',
                'description' => 'Premium fox nuts from Mithilanchal — naturally sourced, carefully processed, and made for homes and businesses that want the real taste of Bihar.',
                'background_image' => 'assests/img/hq-bowl.jpg',
                'button_text' => 'Explore Products',
                'button_url' => '/product',
                'button_2_text' => 'Why Choose Us',
                'button_2_url' => '/why-choose-us',
                'tagline' => null,
                'discount_text' => null,
                'discount_subtext' => null,
                'coupon_code' => null,
                'product_image' => null,
                'bowl_image' => null,
                'badge_text' => null,
                'shipping_text' => null,
                'urgency_text' => null,
                'social_proof' => null,
                'features' => null,
                'payload' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'hero_banner',
                'name' => 'Source slide',
                'is_active' => true,
                'sort_order' => 40,
                'show_as_popup' => false,
                'eyebrow' => 'MITHILANCHAL, BIHAR',
                'headline' => 'From pond to pack.',
                'headline_highlight' => 'Quality you can taste.',
                'description' => 'A trusted manufacturer, wholesale supplier and exporter of premium-quality makhana from the heart of Mithila.',
                'background_image' => 'assests/img/hq-white.jpg',
                'button_text' => 'Discover Our Story',
                'button_url' => '/our-story',
                'button_2_text' => 'Contact Us',
                'button_2_url' => '/contact-us',
                'tagline' => null,
                'discount_text' => null,
                'discount_subtext' => null,
                'coupon_code' => null,
                'product_image' => null,
                'bowl_image' => null,
                'badge_text' => null,
                'shipping_text' => null,
                'urgency_text' => null,
                'social_proof' => null,
                'features' => null,
                'payload' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
