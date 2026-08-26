<?php

use App\Models\Blog;
use Illuminate\Support\Facades\Cache;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('lists only active published blogs', function () {
    Blog::factory()->create([
        'title' => 'Visible Post',
        'slug' => 'visible-post',
        'status' => 'published',
        'is_active' => true,
        'published_at' => now()->subDay(),
    ]);

    Blog::factory()->draft()->create([
        'title' => 'Hidden Draft',
        'slug' => 'hidden-draft',
    ]);

    Blog::factory()->inactive()->create([
        'title' => 'Inactive Post',
        'slug' => 'inactive-post',
        'status' => 'published',
    ]);

    $response = $this->get(route('blog.index'));

    $response->assertOk();
    $response->assertSee('Visible Post');
    $response->assertDontSee('Hidden Draft');
    $response->assertDontSee('Inactive Post');
});

it('shows an active published blog by slug and returns 404 for drafts', function () {
    $blog = Blog::factory()->create([
        'title' => 'Makhana Guide',
        'slug' => 'makhana-guide',
        'meta_title' => 'SEO Title for Makhana Guide',
        'meta_description' => 'SEO description for makhana guide',
        'meta_keywords' => 'makhana, fox nuts',
        'status' => 'published',
        'is_active' => true,
        'published_at' => now()->subHour(),
    ]);

    $this->get(route('blog.show', $blog->slug))
        ->assertOk()
        ->assertSee('Makhana Guide')
        ->assertSee('SEO Title for Makhana Guide', false);

    Blog::factory()->draft()->create([
        'slug' => 'draft-slug',
    ]);

    $this->get(route('blog.show', 'draft-slug'))->assertNotFound();
});

it('auto generates a unique slug and clears listing cache after save', function () {
    Cache::put('blogs.index.page.1', 'stale', 600);

    $blog = Blog::factory()->create([
        'title' => 'Fresh Farm Story',
        'slug' => null,
        'status' => 'published',
        'is_active' => true,
        'published_at' => now(),
    ]);

    expect($blog->fresh()->slug)->toBe('fresh-farm-story');
    expect(Cache::get('blogs.index.page.1'))->toBeNull();
});

it('serves a sitemap that includes published blog urls', function () {
    $blog = Blog::factory()->create([
        'slug' => 'sitemap-post',
        'status' => 'published',
        'is_active' => true,
        'published_at' => now(),
    ]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee(route('blog.show', $blog->slug), false);
});
