<?php

use App\Models\CityPage;
use App\Models\MoonshineUser;
use App\MoonShine\Resources\CityPage\CityPageResource;
use App\Support\CityPageBlueprint;
use App\Support\CmsRole;
use App\Support\CmsUser;
use Database\Seeders\CityPageSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('shows a published city page at a clean location slug', function () {
    $city = CityPage::factory()->create([
        'city_name' => 'Patna',
        'state' => 'Bihar',
        'slug' => 'patna',
        'template' => CityPageBlueprint::TEMPLATE_STANDARD,
        'seo_title' => 'Premium Patna Makhana Supplier',
        'meta_description' => 'Buy Patna makhana in bulk from Mallah Makhana.',
    ]);

    $this->get(route('location.show', 'patna'))
        ->assertOk()
        ->assertSee('Premium Patna Makhana Supplier', false)
        ->assertSee('data-city-template="standard"', false)
        ->assertSee('location.css', false)
        ->assertSee('Patna');
});

it('returns 404 for draft, unknown, and deleted city pages', function () {
    $draft = CityPage::factory()->draft()->create([
        'city_name' => 'Ranchi',
        'slug' => 'ranchi',
    ]);

    $this->get(route('location.show', 'ranchi'))->assertNotFound();
    $this->get('/location/not-a-real-city')->assertNotFound();

    $draft->delete();

    $this->get(route('location.show', 'ranchi'))->assertNotFound();
});

it('recovers the location dropdown when city nav cache is stale', function () {
    CityPage::factory()->create([
        'city_name' => 'Patna',
        'slug' => 'patna',
        'seo_title' => 'Premium Patna Makhana Supplier',
    ]);

    Cache::put('city_pages.nav', 'corrupt-eloquent-payload');
    Cache::put(CityPage::NAV_CACHE_KEY, 'corrupt-eloquent-payload');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Location')
        ->assertSee('Patna');

    expect(Cache::get(CityPage::NAV_CACHE_KEY))->toBeArray();
});

it('shows seeded dummy cities in the location dropdown', function () {
    $this->seed(CityPageSeeder::class);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Location')
        ->assertSee('Patna')
        ->assertSee('Darbhanga')
        ->assertSee('Muzaffarpur')
        ->assertSee(route('location.show', 'patna'), false);

    $this->get(route('location.show', 'darbhanga'))
        ->assertOk()
        ->assertSee('Darbhanga');
});

it('lists only published cities in the location dropdown', function () {
    CityPage::factory()->create([
        'city_name' => 'Delhi',
        'slug' => 'delhi',
    ]);
    CityPage::factory()->draft()->create([
        'city_name' => 'Mumbai',
        'slug' => 'mumbai',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Location')
        ->assertSee('Delhi')
        ->assertSee(route('location.show', 'delhi'), false)
        ->assertDontSee('Mumbai')
        ->assertDontSee(route('location.show', 'mumbai'), false);
});

it('does not render disabled sections and respects section order', function () {
    $city = CityPage::factory()->create([
        'city_name' => 'Jaipur',
        'slug' => 'jaipur',
    ]);

    $city->sections()->where('section_type', CityPageBlueprint::FAQ)->update(['is_enabled' => false]);
    $city->sections()->where('section_type', CityPageBlueprint::PROCESS)->update(['display_order' => 5, 'is_enabled' => true]);
    $city->sections()->where('section_type', CityPageBlueprint::ABOUT)->update(['display_order' => 80, 'is_enabled' => true]);
    $city->unsetRelation('sections');

    $html = $this->get(route('location.show', 'jaipur'))->assertOk()->getContent();

    expect($html)->not->toContain('Frequently Asked Questions');
    expect($html)->toContain('city-process');
    expect($html)->toContain('Jaipur Makhana');
    expect(strpos($html, 'city-process'))->toBeLessThan(strpos($html, 'city-about'));
});

it('renders each selected template independently', function () {
    CityPage::factory()->create([
        'city_name' => 'Kolkata',
        'slug' => 'kolkata',
        'template' => CityPageBlueprint::TEMPLATE_STANDARD,
    ]);
    CityPage::factory()->modern()->create([
        'city_name' => 'Lucknow',
        'slug' => 'lucknow',
    ]);
    CityPage::factory()->minimal()->create([
        'city_name' => 'Pune',
        'slug' => 'pune',
    ]);

    $this->get(route('location.show', 'kolkata'))->assertOk()->assertSee('data-city-template="standard"', false);
    $this->get(route('location.show', 'lucknow'))->assertOk()->assertSee('data-city-template="modern"', false);
    $this->get(route('location.show', 'pune'))
        ->assertOk()
        ->assertSee('data-city-template="minimal"', false);
});

it('keeps slugs unique and stamps a publish date', function () {
    $first = CityPage::factory()->create([
        'city_name' => 'Gaya',
        'slug' => 'shared-city',
    ]);
    $second = CityPage::factory()->draft()->create([
        'city_name' => 'Bhagalpur',
        'slug' => 'shared-city',
        'published_at' => null,
    ]);

    expect($first->fresh()->slug)->toBe('shared-city');
    expect($second->fresh()->slug)->toBe('shared-city-1');

    $second->publish();

    expect($second->fresh()->isPublished())->toBeTrue();
    expect($second->fresh()->published_at)->not->toBeNull();
});

it('rejects a second page for the same city name', function () {
    CityPage::factory()->create([
        'city_name' => 'Muzaffarpur',
        'slug' => 'muzaffarpur',
    ]);

    CityPage::factory()->create([
        'city_name' => 'Muzaffarpur',
        'slug' => 'muzaffarpur-west',
    ]);
})->throws(QueryException::class);

it('lets an admin preview a draft city that stays out of the public nav', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'Location Admin',
        'email' => 'location-admin@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    $city = CityPage::factory()->draft()->create([
        'city_name' => 'Noida',
        'slug' => 'noida',
    ]);

    $this->get(route('location.show', 'noida'))->assertNotFound();

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Noida');

    $this->actingAs($admin, 'moonshine')
        ->get(route('cms.locations.preview', $city))
        ->assertOk()
        ->assertSee('Premium Noida Makhana')
        ->assertSee('CMS preview')
        ->assertSee('noindex, nofollow', false);
});

it('blocks guests and authors from city page preview', function () {
    $author = MoonshineUser::query()->create([
        'name' => 'Location Author',
        'email' => 'location-author@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $city = CityPage::factory()->draft()->create([
        'city_name' => 'Surat',
        'slug' => 'surat',
    ]);

    $this->get(route('cms.locations.preview', $city))->assertRedirect();

    $this->actingAs($author, 'moonshine')
        ->get(route('cms.locations.preview', $city))
        ->assertForbidden();
});

it('lists city pages for admins and hides the module from authors', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'City Admin',
        'email' => 'city-admin@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);
    $author = MoonshineUser::query()->create([
        'name' => 'City Author',
        'email' => 'city-author@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $this->actingAs($admin, 'moonshine');
    expect(CmsUser::isAdmin())->toBeTrue();
    expect(app(CityPageResource::class)->canSee())->toBeTrue();

    $this->actingAs($author, 'moonshine');
    expect(CmsUser::isAdmin())->toBeFalse();
    expect(app(CityPageResource::class)->canSee())->toBeFalse();
});

it('includes published city urls in the sitemap and writes seo metadata', function () {
    $city = CityPage::factory()->create([
        'city_name' => 'Indore',
        'slug' => 'indore',
        'seo_title' => 'Indore Makhana Supplier',
        'meta_description' => 'Wholesale makhana from Indore.',
        'og_title' => 'OG Indore Makhana',
    ]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('location.show', 'indore'), false);

    $this->get(route('location.show', 'indore'))
        ->assertOk()
        ->assertSee('<title>Indore Makhana Supplier</title>', false)
        ->assertSee('Wholesale makhana from Indore.', false)
        ->assertSee('OG Indore Makhana', false)
        ->assertSee('schema.org', false)
        ->assertSee('WebPage', false)
        ->assertSee($city->canonicalUrl(), false);
});

it('still renders a page when optional images and most sections are off', function () {
    $city = CityPage::factory()->create([
        'city_name' => 'Nagpur',
        'slug' => 'nagpur',
    ]);

    $city->sections()->update(['is_enabled' => false]);
    $city->sections()->where('section_type', CityPageBlueprint::ABOUT)->update([
        'is_enabled' => true,
        'content' => [
            'title' => 'Trusted Nagpur Makhana Supplier',
            'description' => 'Text-only about section with no photos.',
            'image' => null,
            'inset_image' => null,
        ],
    ]);
    $city->unsetRelation('sections');

    $this->get(route('location.show', 'nagpur'))
        ->assertOk()
        ->assertSee('Trusted Nagpur Makhana Supplier')
        ->assertSee('<h1>', false)
        ->assertDontSee('city-hero-visual', false);
});

it('turns unpublished cities back into drafts and removes them from navigation', function () {
    $city = CityPage::factory()->create([
        'city_name' => 'Kanpur',
        'slug' => 'kanpur',
    ]);

    $this->get(route('home'))->assertSee('Kanpur');

    $city->unpublish();
    CityPage::clearCaches();

    $this->get(route('location.show', 'kanpur'))->assertNotFound();
    $this->get(route('home'))->assertDontSee('>Kanpur<', false);
});
