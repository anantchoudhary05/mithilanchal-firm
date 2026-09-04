<?php

use App\Models\HomepageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders homepage hero slides from the backend', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-hero-carousel', false)
        ->assertSee('Grown with care')
        ->assertSee('Discover Our Story')
        ->assertSee('Explore Products');
});

it('shows an active banner headline and hides inactive banners', function () {
    HomepageSection::factory()->create([
        'name' => 'Custom banner',
        'headline' => 'Unique Pond Harvest Banner',
        'headline_highlight' => 'Only from CMS.',
        'sort_order' => 5,
        'is_active' => true,
    ]);

    HomepageSection::factory()->inactive()->create([
        'name' => 'Hidden banner',
        'headline' => 'Hidden Banner Should Not Appear',
        'sort_order' => 1,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Unique Pond Harvest Banner')
        ->assertSee('Only from CMS.')
        ->assertDontSee('Hidden Banner Should Not Appear');
});

it('renders the offer card in the popup and as a full-size carousel slide', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-offer-popup', false)
        ->assertSee('offer-card--popup', false)
        ->assertSee('offer-card--slide', false)
        ->assertSee('hero-slide--offer', false)
        ->assertSee('Special Welcome Offer!')
        ->assertSee('MAKHANA15')
        ->assertSee('15% OFF')
        ->assertSee('Goodness in every bite!')
        ->assertSee('SHOP NOW & SAVE')
        ->assertSee('data-deferred-offer="true"', false);
});

it('does not popup an inactive or non-popup offer', function () {
    HomepageSection::query()->offers()->update(['is_active' => false, 'show_as_popup' => false]);

    HomepageSection::factory()->offer()->create([
        'headline' => 'Carousel Only Crunch Deal',
        'coupon_code' => 'CRUNCH10',
        'show_as_popup' => false,
        'is_active' => true,
        'sort_order' => 15,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Carousel Only Crunch Deal')
        ->assertSee('CRUNCH10')
        ->assertDontSee('data-offer-popup', false)
        ->assertDontSee('data-deferred-offer="true"', false);
});

it('falls back to the default banner when no banner pages are active', function () {
    HomepageSection::query()->banners()->update(['is_active' => false]);
    HomepageSection::query()->offers()->update(['is_active' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Grown with care')
        ->assertSee('data-hero-carousel', false);
});
