<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Support\CmsUser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        // Do not cache the paginator itself — DB/file cache can corrupt Eloquent
        // models into plain strings after unserialize (breaks $blog->featured_image_url).
        $blogs = Blog::published()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        return view('blog.index', [
            'blogs' => $blogs,
            'meta_title' => 'Blog | Mithilanchal Farms',
            'meta_description' => 'Discover healthy recipes, nutritional facts about Fox Nuts, and insights into our premium makhana processing.',
            'meta_keywords' => 'Fox Nuts, Makhana, Healthy Recipes, Nutritional Facts, Premium Makhana Processing',
        ]);
    }

    public function show(string $slug): View
    {
        $blog = Blog::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedBlogs = $blog->relatedBlogs();

        return view('blog.show', [
            'blog' => $blog,
            'relatedBlogs' => $relatedBlogs,
            'meta_title' => $blog->seo_title,
            'meta_description' => $blog->meta_description ?: $blog->excerpt,
            'meta_keywords' => $blog->meta_keywords,
            'canonical_url' => $blog->canonical_url ?: route('blog.show', $blog->slug),
            'robots' => 'index, follow',
            'custom_schema' => $blog->custom_schema,
            'isPreview' => false,
        ]);
    }

    public function preview(Blog $blog): View
    {
        abort_unless(CmsUser::check(), 403);

        if (CmsUser::isAuthor()) {
            abort_unless($blog->isOwnedBy(CmsUser::id()), 403);
        } elseif (! CmsUser::isAdmin()) {
            abort(403);
        }

        return view('blog.show', [
            'blog' => $blog,
            'relatedBlogs' => $blog->relatedBlogs(),
            'meta_title' => 'Preview: '.$blog->seo_title,
            'meta_description' => $blog->meta_description ?: $blog->excerpt,
            'meta_keywords' => $blog->meta_keywords,
            'canonical_url' => $blog->canonical_url ?: route('blog.show', $blog->slug),
            'robots' => 'noindex, nofollow',
            'custom_schema' => $blog->isLive() ? $blog->custom_schema : null,
            'isPreview' => true,
        ]);
    }
}
