<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\CityPage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = Cache::remember('blogs.sitemap', now()->addHour(), function () {
            $static = [
                ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
                ['loc' => route('Product'), 'changefreq' => 'weekly', 'priority' => '0.9'],
                ['loc' => route('WhyChooseUs'), 'changefreq' => 'monthly', 'priority' => '0.7'],
                ['loc' => route('OurStory'), 'changefreq' => 'monthly', 'priority' => '0.7'],
                ['loc' => route('ContactUs'), 'changefreq' => 'monthly', 'priority' => '0.6'],
                ['loc' => route('blog.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ];

            $posts = Blog::published()
                ->orderByDesc('updated_at')
                ->get(['slug', 'updated_at', 'published_at'])
                ->map(fn (Blog $blog) => [
                    'loc' => route('blog.show', $blog->slug),
                    'lastmod' => optional($blog->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ])
                ->all();

            $cities = CityPage::query()
                ->published()
                ->orderByDesc('updated_at')
                ->get(['slug', 'updated_at'])
                ->map(fn (CityPage $city) => [
                    'loc' => route('location.show', $city->slug),
                    'lastmod' => optional($city->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ])
                ->all();

            return array_merge($static, $posts, $cities);
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
