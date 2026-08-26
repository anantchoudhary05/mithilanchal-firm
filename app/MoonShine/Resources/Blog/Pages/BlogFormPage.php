<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Blog\Pages;

use App\Models\Blog;
use App\MoonShine\Resources\Blog\BlogResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Url;

/**
 * @extends FormPage<BlogResource, Blog>
 */
final class BlogFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $blogOptions = Blog::query()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();

        return [
            Box::make([
                Tabs::make([
                    Tab::make('Blog Content', [
                        ID::make(),

                        Text::make('Blog Title', 'title')
                            ->required(),

                        Text::make('URL Slug', 'slug')
                            ->hint('Leave empty to auto-generate from title'),

                        TinyMce::make('Main Content Editor', 'content')
                            ->required()
                            ->hint('Select text → Ctrl+K to insert a link. Use Rel dropdown for DoFollow / NoFollow / Sponsored / UGC.'),

                        Textarea::make('Excerpt', 'excerpt')
                            ->hint('Short summary for listing cards'),

                        Image::make('Featured Image', 'featured_image')
                            ->disk('public')
                            ->dir('blogs')
                            ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                            ->removable(),

                        Text::make('Image ALT Text', 'image_alt'),

                        Text::make('Author Name', 'author_name'),

                        Textarea::make('Author Profile', 'author_profile'),

                        Select::make('Status', 'status')
                            ->options([
                                'draft' => 'Draft',
                                'review' => 'Review',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('published')
                            ->hint('Use Published + Active ON to show on website')
                            ->required(),

                        Select::make('Content Type', 'content_type')
                            ->options([
                                'guide' => 'Guide',
                                'news' => 'News',
                                'case_study' => 'Case Study',
                                'technical' => 'Technical',
                                'comparison' => 'Comparison',
                                'product_education' => 'Product Education',
                            ]),

                        Date::make('Published / Scheduled Date', 'published_at')
                            ->withTime()
                            ->hint('For Published posts this is only the display date. For Scheduled, wait until this time.'),

                        Switcher::make('Active (visible on website)', 'is_active')
                            ->default(true),
                        Switcher::make('Featured Post', 'is_featured'),

                        Textarea::make('Heading Structure', 'heading_structure')
                            ->hint('Auto-filled from content headings if left empty (H1: ..., H2: ...)'),

                        Textarea::make('Table of Contents (HTML)', 'table_of_contents')
                            ->hint('Auto-generated from H2/H3 in content on save'),
                    ])->icon('document-text'),

                    Tab::make('SEO Control', [
                        Text::make('SEO Title', 'meta_title'),
                        Textarea::make('Meta Description', 'meta_description'),
                        Text::make('Meta Keywords', 'meta_keywords'),
                        Url::make('Canonical URL', 'canonical_url')
                            ->hint('Leave empty to use the blog page URL'),
                        Textarea::make('Custom Schema (JSON-LD)', 'custom_schema')
                            ->hint('Paste JSON-LD schema markup for this page'),
                    ])->icon('globe-alt'),

                    Tab::make('Related & FAQ', [
                        Select::make('Related Blogs', 'related_blog_ids')
                            ->options($blogOptions)
                            ->multiple()
                            ->searchable(),

                        Json::make('Related Products', 'related_products')
                            ->fields([
                                Text::make('Product Title', 'title'),
                                Text::make('Product URL', 'url'),
                            ])
                            ->removable(),

                        Json::make('FAQ Section', 'faq')
                            ->fields([
                                Text::make('Question', 'question'),
                                Textarea::make('Answer', 'answer'),
                            ])
                            ->removable(),
                    ])->icon('link'),
                ]),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,review,scheduled,published'],
            'content_type' => ['nullable', 'in:guide,news,case_study,technical,comparison,product_education'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
