<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('serves public marketing pages', function (string $routeName) {
    $this->get(route($routeName))->assertOk();
})->with([
    'home',
    'Product',
    'WhyChooseUs',
    'OurStory',
    'ContactUs',
    'blog.index',
]);

it('does not keep leftover static html hrefs on converted pages', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('ourstory.html')
        ->assertDontSee('index.html#products');

    $this->get(route('Product'))
        ->assertOk()
        ->assertDontSee('ContactUs.html');

    $this->get(route('WhyChooseUs'))
        ->assertOk()
        ->assertDontSee('ContactUs.html');
});

it('serves robots.txt with the current app sitemap url', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee(url('/sitemap.xml'), false);
});

it('does not show a cms login button on the public navbar', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('nav-login')
        ->assertDontSee(route('moonshine.login'), false);
});

it('loads the shared public theme and skip link', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('assests/css/theme.css', false)
        ->assertSee('Skip to content')
        ->assertSee('fa-whatsapp', false)
        ->assertSee('Grown with care');
});

it('renders counting stats on the homepage', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('stat-number', false)
        ->assertSee('data-count="4"', false)
        ->assertSee('data-count="100"', false)
        ->assertSee('Years of care');
});
