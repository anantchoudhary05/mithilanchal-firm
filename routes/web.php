<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

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
