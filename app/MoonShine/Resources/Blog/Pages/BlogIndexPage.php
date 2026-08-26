<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Blog\Pages;

use App\Models\Blog;
use App\MoonShine\Resources\Blog\BlogResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;

/**
 * @extends IndexPage<BlogResource>
 */
final class BlogIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Image::make('Image', 'featured_image')
                ->disk('public')
                ->dir('blogs')
                ->modifyRawValue(fn (?string $raw): string => $raw ?? ''),
            Text::make('Title', 'title')->sortable(),
            Text::make('Slug', 'slug'),
            Select::make('Status', 'status')->options([
                'draft' => 'Draft',
                'review' => 'Review',
                'scheduled' => 'Scheduled',
                'published' => 'Published',
            ]),
            Switcher::make('Active', 'is_active')->updateOnPreview(),
            Switcher::make('Featured', 'is_featured')->updateOnPreview(),
            Date::make('Published', 'published_at')->format('d M Y')->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Select::make('Status', 'status')->options([
                'draft' => 'Draft',
                'review' => 'Review',
                'scheduled' => 'Scheduled',
                'published' => 'Published',
            ]),
            Select::make('Content Type', 'content_type')->options([
                'guide' => 'Guide',
                'news' => 'News',
                'case_study' => 'Case Study',
                'technical' => 'Technical',
                'comparison' => 'Comparison',
                'product_education' => 'Product Education',
            ]),
            Switcher::make('Active', 'is_active'),
            Switcher::make('Featured', 'is_featured'),
        ];
    }
}
