<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);
        $content = '<h2>'.fake()->sentence(4).'</h2><p>'.implode('</p><p>', fake()->paragraphs(4)).'</p><h3>'.fake()->sentence(3).'</h3><p>'.fake()->paragraph().'</p>';

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => fake()->sentence(18),
            'featured_image' => null,
            'image_alt' => $title,
            'author_name' => fake()->name(),
            'author_profile' => fake()->sentence(12),
            'author_id' => null,
            'status' => 'published',
            'content_type' => fake()->randomElement(['guide', 'news', 'case_study', 'technical', 'comparison', 'product_education']),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
            'is_sticky' => false,
            'published_at' => now()->subDays(fake()->numberBetween(0, 30)),
            'meta_title' => $title.' | Mithilanchal Farms',
            'meta_description' => fake()->sentence(20),
            'meta_keywords' => 'makhana, fox nuts, mithilanchal farms',
            'canonical_url' => null,
            'link_attribute' => 'dofollow',
            'custom_schema' => null,
            'faq' => [
                ['question' => 'What is makhana?', 'answer' => 'Makhana (fox nuts) are roasted lotus seeds from Mithila.'],
                ['question' => 'Are fox nuts healthy?', 'answer' => 'Yes, they are high in protein and low in fat when roasted.'],
            ],
            'related_products' => [
                [
                    'title' => 'Premium Makhana',
                    'grade' => 'Premium Grade',
                    'badge' => 'Best Seller',
                    'description' => 'Large, clean and carefully graded Makhana suitable for premium retail and food brands.',
                    'image' => 'assests/img/hq-white.jpg',
                    'url' => '/products',
                ],
                [
                    'title' => 'Roasted Makhana',
                    'grade' => 'Roasted',
                    'badge' => 'Ready to Eat',
                    'description' => 'Lightly roasted Makhana with a delicious crunch, perfect for healthy snacking.',
                    'image' => 'assests/img/hq-roasted.jpg',
                    'url' => '/products',
                ],
            ],
            'related_blog_ids' => [],
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'is_active' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
