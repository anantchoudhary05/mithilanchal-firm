<?php

use App\Models\MoonshineUser;
use App\Support\CmsRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function editorJpeg(string $name = 'makhana.jpg'): UploadedFile
{
    // 1x1 JPEG so tests do not need the GD extension.
    $jpeg = hex2bin('ffd8ffe000104a46494600010100000100010000ffdb004300080606070605080707070909080a0c140d0c0b0b0c1912130f141d1a1f1e1d1a1c1c20242e2720222c231c1c2837292c30313434341f27393d38323c2e333432ffc0000b080001000101011100ffc40014000100000000000000000000000000000009ffc4001400100100000000000000000000000000000000ffda00080001000100003f00d2cf20ffd9');

    return UploadedFile::fake()->createWithContent($name, $jpeg ?: 'jpeg');
}

it('lets a cms editor upload an image for the blog content editor', function () {
    Storage::fake('public');

    $admin = MoonshineUser::query()->create([
        'name' => 'Upload Admin',
        'email' => 'upload-admin@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    $response = $this->actingAs($admin, 'moonshine')
        ->post(route('cms.tinymce.upload'), [
            'file' => editorJpeg(),
        ]);

    $response->assertOk()->assertJsonStructure(['location']);
    expect($response->json('location'))->toContain('/storage/blogs/content/');

    $relative = ltrim(parse_url((string) $response->json('location'), PHP_URL_PATH) ?: '', '/');
    $relative = preg_replace('#^storage/#', '', $relative) ?? $relative;
    Storage::disk('public')->assertExists($relative);
});

it('lets an author upload an image for the blog content editor', function () {
    Storage::fake('public');

    $author = MoonshineUser::query()->create([
        'name' => 'Upload Author',
        'email' => 'upload-author@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $this->actingAs($author, 'moonshine')
        ->post(route('cms.tinymce.upload'), [
            'file' => editorJpeg('story.jpg'),
        ])
        ->assertOk()
        ->assertJsonStructure(['location']);
});

it('rejects a non image upload in the blog content editor', function () {
    Storage::fake('public');

    $admin = MoonshineUser::query()->create([
        'name' => 'Upload Admin',
        'email' => 'upload-pdf@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    $this->actingAs($admin, 'moonshine')
        ->post(route('cms.tinymce.upload'), [
            'file' => UploadedFile::fake()->create('notes.pdf', 120, 'application/pdf'),
        ])
        ->assertSessionHasErrors('file');
});

it('sends guests from the tinymce upload to the login page', function () {
    Storage::fake('public');

    $this->post(route('cms.tinymce.upload'), [
        'file' => editorJpeg('guest.jpg'),
    ])->assertRedirect(route('moonshine.login'));
});
