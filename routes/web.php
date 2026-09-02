<?php

use App\Http\Controllers\AdminLogoutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TinyMceImageUploadController;
use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\Http\Middleware\Authenticate as MoonShineAuthenticate;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/product', function () {
    return view('Products');
})->name('Product');

Route::get('/why-choose-us', function () {
    return view('WhyChooseUs');
})->name('WhyChooseUs');

Route::get('/our-story', function () {
    return view('ourstory');
})->name('OurStory');

Route::get('/contact-us', function () {
    return view('ContactUs');
})->name('ContactUs');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/robots.txt', function () {
    $body = implode("\n", [
        'User-agent: *',
        'Disallow:',
        '',
        'Sitemap: '.url('/sitemap.xml'),
    ])."\n";

    return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

Route::get('/cms/logout', AdminLogoutController::class)->name('cms.logout');

Route::middleware(MoonShineAuthenticate::class)->group(function (): void {
    Route::get('/cms/blogs/{blog}/preview', [BlogController::class, 'preview'])
        ->name('cms.blogs.preview');
    Route::post('/cms/tinymce/upload', TinyMceImageUploadController::class)
        ->name('cms.tinymce.upload');
});
