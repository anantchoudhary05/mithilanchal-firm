<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Blog::factory()->count(6)->create();

        Blog::factory()->create([
            'title' => 'Health Benefits of Premium Mithila Makhana',
            'slug' => 'health-benefits-of-premium-mithila-makhana',
            'author_name' => 'Mithilanchal Farms',
            'content_type' => 'guide',
            'is_featured' => true,
            'meta_title' => 'Health Benefits of Mithila Makhana | Mithilanchal Farms',
            'meta_description' => 'Learn why premium fox nuts from Darbhanga are a protein-rich, heart-friendly snack.',
            'related_blog_ids' => $posts->take(2)->pluck('id')->all(),
            'custom_schema' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => 'Health Benefits of Premium Mithila Makhana',
                'author' => [
                    '@type' => 'Organization',
                    'name' => 'Mithilanchal Farms Private Limited',
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
        ]);

        Blog::factory()->draft()->create([
            'title' => 'Draft: Upcoming Harvest Season Notes',
        ]);

        Blog::factory()->inactive()->create([
            'title' => 'Inactive Published Archive Example',
            'status' => 'published',
        ]);
    }
}
