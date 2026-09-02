<?php

use App\Models\MoonshineUser;
use App\Support\CmsRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use MoonShine\Laravel\Models\MoonshineUserRole;

uses(RefreshDatabase::class);

it('creates the author moonshine role', function () {
    expect(MoonshineUserRole::query()->where('name', 'Author')->exists())->toBeTrue();
    expect(CmsRole::authorId())->toBeGreaterThan(CmsRole::ADMIN);
});

it('lets an admin create an author account', function () {
    $author = MoonshineUser::query()->create([
        'name' => 'Rina Author',
        'email' => 'rina@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
        'bio' => 'Farm stories from Mithilanchal.',
    ]);

    expect($author->isAuthor())->toBeTrue();
    expect($author->isAdmin())->toBeFalse();
    expect($author->blogs()->count())->toBe(0);
});

it('logs a cms user out and returns to the login screen', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'Site Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    $this->actingAs($admin, 'moonshine');

    $this->get(route('cms.logout'))
        ->assertRedirect(route('moonshine.login'));

    $this->assertGuest('moonshine');
});

it('shows admin and author role options on the cms login page', function () {
    $this->get(route('moonshine.login'))
        ->assertOk()
        ->assertSee('Login as')
        ->assertSee('Admin')
        ->assertSee('Author');
});

it('logs an admin in only when the admin role is selected', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'Site Admin',
        'email' => 'admin-login@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    $this->from(route('moonshine.login'))
        ->post(route('moonshine.authenticate'), [
            'username' => 'admin-login@example.com',
            'password' => 'password',
            'role' => 'author',
        ])
        ->assertSessionHasErrors('role');

    $this->assertGuest('moonshine');

    $this->post(route('moonshine.authenticate'), [
        'username' => 'admin-login@example.com',
        'password' => 'password',
        'role' => 'admin',
    ])->assertRedirect();

    $this->assertAuthenticatedAs($admin, 'moonshine');
});

it('logs an author in only when the author role is selected', function () {
    $author = MoonshineUser::query()->create([
        'name' => 'Rina Author',
        'email' => 'author-login@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $this->from(route('moonshine.login'))
        ->post(route('moonshine.authenticate'), [
            'username' => 'author-login@example.com',
            'password' => 'password',
            'role' => 'admin',
        ])
        ->assertSessionHasErrors('role');

    $this->assertGuest('moonshine');

    $this->post(route('moonshine.authenticate'), [
        'username' => 'author-login@example.com',
        'password' => 'password',
        'role' => 'author',
    ])->assertRedirect();

    $this->assertAuthenticatedAs($author, 'moonshine');
});

it('rejects cms login when no role is selected', function () {
    MoonshineUser::query()->create([
        'name' => 'Site Admin',
        'email' => 'norole@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    $this->from(route('moonshine.login'))
        ->post(route('moonshine.authenticate'), [
            'username' => 'norole@example.com',
            'password' => 'password',
        ])
        ->assertSessionHasErrors('role');

    $this->assertGuest('moonshine');
});
