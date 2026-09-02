<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Blog\Pages;

use App\Models\Blog;
use App\Models\MoonshineUser;
use App\MoonShine\Resources\Blog\BlogResource;
use App\Support\CmsUser;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Preview;
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

        $authorOptions = MoonshineUser::query()
            ->with('moonshineUserRole')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (MoonshineUser $user): array {
                $role = $user->moonshineUserRole?->name ?: 'User';

                return [$user->id => $user->name.' ('.$role.')'];
            })
            ->all();

        $isAdmin = CmsUser::isAdmin();

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
                            ->hint('Select text → Ctrl+K for a link (Rel = DoFollow / NoFollow). Image opens an Upload tab so you can pick a photo from your computer. Width and height are not required.'),

                        Textarea::make('Excerpt', 'excerpt')
                            ->hint('Short summary for listing cards'),

                        Image::make('Featured Image', 'featured_image')
                            ->disk('public')
                            ->dir('blogs')
                            ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'gif'])
                            ->removable(),

                        Text::make('Image ALT Text', 'image_alt'),

                        Select::make('Author', 'author_id')
                            ->options($authorOptions)
                            ->searchable()
                            ->required()
                            ->hint('The public byline uses this author’s name. Create authors under People → Authors.')
                            ->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),

                        Preview::make('Author', 'author_name')
                            ->hint('Posts you submit are published under your name after an admin approves them.')
                            ->canSee(static fn (mixed $item): bool => ! CmsUser::isAdmin())
                            ->changeFill(static fn (mixed $data, mixed $field): string => CmsUser::user()?->name ?: 'You'),

                        Textarea::make('Author Profile', 'author_profile')
                            ->hint('Shown on the public article page. Leave empty to use the author’s bio.')
                            ->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),

                        Preview::make('Live post')
                            ->changeFill(static fn (): string => 'This post is live. Saving your edits will take it off the website until an admin approves them again.')
                            ->canSee(static function (mixed $item): bool {
                                $blog = $item instanceof Blog ? $item : null;

                                return CmsUser::isAuthor() && $blog?->isLive() === true;
                            }),

                        Select::make('Status', 'status')
                            ->options($isAdmin
                                ? [
                                    'draft' => 'Draft',
                                    'review' => 'Awaiting approval',
                                    'scheduled' => 'Scheduled',
                                    'published' => 'Approved',
                                ]
                                : [
                                    'draft' => 'Draft',
                                    'review' => 'Submit for approval',
                                ])
                            ->default($isAdmin ? 'published' : 'draft')
                            ->changeFill(static function (mixed $data, mixed $field): mixed {
                                $status = $data instanceof Blog ? $data->status : null;

                                if (CmsUser::isAuthor() && filled($status) && ! in_array($status, ['draft', 'review'], true)) {
                                    return 'review';
                                }

                                return $status;
                            })
                            ->hint($isAdmin
                                ? 'Use Preview to check the page first. Approved + Active ON makes it live, or use the green Approve button on the list.'
                                : 'Save as Draft while writing. Use Preview to check the page, then Submit for approval when it is ready. Editing a live post sends it back for approval.')
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
                            ->hint('For Published posts this is only the display date. For Scheduled, wait until this time.')
                            ->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),

                        Switcher::make('Active (visible on website)', 'is_active')
                            ->default($isAdmin)
                            ->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),
                        Switcher::make('Featured Post', 'is_featured')
                            ->canSee(static fn (mixed $item): bool => CmsUser::isAdmin()),

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
        $statusRule = CmsUser::isAdmin()
            ? ['required', 'in:draft,review,scheduled,published']
            : ['required', 'in:draft,review'];

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'author_id' => CmsUser::isAdmin()
                ? ['required', 'integer', 'exists:moonshine_users,id']
                : ['nullable'],
            'status' => $statusRule,
            'content_type' => ['nullable', 'in:guide,news,case_study,technical,comparison,product_education'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons()->prepend($this->getResource()->getPreviewButton());
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->customAttributes(['title' => 'Delete', 'aria-label' => 'Delete']);
    }
}
