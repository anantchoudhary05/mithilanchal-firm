<?php

use App\Models\Blog;
use App\Models\MoonshineUser;
use App\Support\CmsRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

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

it('auto generates a unique slug and clears sitemap cache after save', function () {
    Cache::put('blogs.sitemap', 'stale', 600);

    $blog = Blog::factory()->create([
        'title' => 'Fresh Farm Story',
        'slug' => null,
        'status' => 'published',
        'is_active' => true,
        'published_at' => now(),
    ]);

    expect($blog->fresh()->slug)->toBe('fresh-farm-story');
    expect(Cache::get('blogs.sitemap'))->toBeNull();
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

it('lists featured posts before regular posts', function () {
    Blog::factory()->create([
        'title' => 'Regular Post',
        'slug' => 'regular-post',
        'status' => 'published',
        'is_active' => true,
        'is_featured' => false,
        'published_at' => now()->subDay(),
    ]);

    Blog::factory()->create([
        'title' => 'Featured Post',
        'slug' => 'featured-post',
        'status' => 'published',
        'is_active' => true,
        'is_featured' => true,
        'published_at' => now()->subDays(2),
    ]);

    $html = $this->get(route('blog.index'))->assertOk()->getContent();

    expect(strpos($html, 'Featured Post'))->toBeLessThan(strpos($html, 'Regular Post'));
    expect($html)->not->toContain('Pinned');
});

it('lets an admin preview an unpublished post that the public cannot see', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'Preview Admin',
        'email' => 'preview-admin@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    $blog = Blog::factory()->draft()->create([
        'title' => 'Draft Preview Title',
        'slug' => 'draft-preview-title',
    ]);

    $this->get(route('blog.show', $blog->slug))->assertNotFound();

    $this->actingAs($admin, 'moonshine')
        ->get(route('cms.blogs.preview', $blog))
        ->assertOk()
        ->assertSee('Draft Preview Title')
        ->assertSee('CMS preview')
        ->assertSee('noindex, nofollow', false);
});

it('lets an author preview only their own unpublished post', function () {
    $author = MoonshineUser::query()->create([
        'name' => 'Own Author',
        'email' => 'own-author@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $other = MoonshineUser::query()->create([
        'name' => 'Other Author',
        'email' => 'other-author@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $mine = Blog::factory()->create([
        'title' => 'My Pending Article',
        'slug' => 'my-pending-article',
        'author_id' => $author->id,
        'status' => 'review',
        'is_active' => false,
    ]);

    $theirs = Blog::factory()->create([
        'title' => 'Someone Else Pending',
        'slug' => 'someone-else-pending',
        'author_id' => $other->id,
        'status' => 'review',
        'is_active' => false,
    ]);

    $this->actingAs($author, 'moonshine')
        ->get(route('cms.blogs.preview', $mine))
        ->assertOk()
        ->assertSee('My Pending Article');

    $this->actingAs($author, 'moonshine')
        ->get(route('cms.blogs.preview', $theirs))
        ->assertForbidden();
});

it('sends guests from the cms preview to the login page', function () {
    $blog = Blog::factory()->draft()->create([
        'slug' => 'secret-draft-preview',
    ]);

    $this->get(route('cms.blogs.preview', $blog))
        ->assertRedirect(route('moonshine.login'));
});

it('copies the selected cms author name onto the public byline', function () {
    $authorRoleId = CmsRole::authorId();

    $author = MoonshineUser::query()->create([
        'name' => 'Mithila Writer',
        'email' => 'writer@example.com',
        'password' => 'password',
        'moonshine_user_role_id' => $authorRoleId,
        'bio' => 'Writes about fox nuts from Darbhanga.',
    ]);

    $blog = Blog::factory()->create([
        'author_id' => $author->id,
        'author_name' => null,
        'author_profile' => null,
        'status' => 'published',
        'is_active' => true,
    ]);

    $blog->refresh();

    expect($blog->author_name)->toBe('Mithila Writer');
    expect($blog->author_profile)->toBe('Writes about fox nuts from Darbhanga.');
});

it('approves a pending blog and makes it live on the website', function () {
    $blog = Blog::factory()->create([
        'title' => 'Needs Approval',
        'slug' => 'needs-approval',
        'status' => 'review',
        'is_active' => false,
        'published_at' => null,
    ]);

    $this->get(route('blog.show', $blog->slug))->assertNotFound();

    $blog->approve();
    $blog->refresh();

    expect($blog->status)->toBe('published')
        ->and($blog->is_active)->toBeTrue()
        ->and($blog->published_at)->not->toBeNull()
        ->and($blog->isAwaitingApproval())->toBeFalse();

    $this->get(route('blog.show', $blog->slug))->assertOk()->assertSee('Needs Approval');
});

it('maps blog statuses to short cms labels', function () {
    expect(Blog::factory()->create(['status' => 'review'])->statusLabel())->toBe('Pending');
    expect(Blog::factory()->create(['status' => 'published', 'is_active' => true])->statusLabel())->toBe('Approved');
    expect(Blog::factory()->draft()->create()->statusLabel())->toBe('Draft');
});

it('lets an author edit their own approved post but not delete it', function () {
    $author = MoonshineUser::query()->create([
        'name' => 'Edit Author',
        'email' => 'edit-author@example.com',
        'password' => 'password',
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $other = MoonshineUser::query()->create([
        'name' => 'Other Writer',
        'email' => 'other-writer@example.com',
        'password' => 'password',
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $ownedLive = Blog::factory()->create([
        'author_id' => $author->id,
        'status' => 'published',
        'is_active' => true,
    ]);

    $ownedDraft = Blog::factory()->draft()->create([
        'author_id' => $author->id,
    ]);

    $someoneElse = Blog::factory()->draft()->create([
        'author_id' => $other->id,
    ]);

    expect($ownedLive->authorCanEdit($author->id))->toBeTrue()
        ->and($ownedLive->authorCanDelete($author->id))->toBeFalse()
        ->and($ownedDraft->authorCanEdit($author->id))->toBeTrue()
        ->and($ownedDraft->authorCanDelete($author->id))->toBeTrue()
        ->and($someoneElse->authorCanEdit($author->id))->toBeFalse()
        ->and($someoneElse->authorCanDelete($author->id))->toBeFalse();
});
