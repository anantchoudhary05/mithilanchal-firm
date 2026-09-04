<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\ContactLead;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ProductCatalog
{
    /**
     * @return list<array{title: string, description: string, image: string, url: string, badge: string, grade: string}>
     */
    public static function items(): array
    {
        $productsUrl = route('Product');

        return [
            [
                'title' => 'Premium Raw Makhana',
                'description' => 'Naturally sourced Makhana carefully selected for size, texture and quality.',
                'image' => 'assests/img/hq-grains.jpg',
                'url' => $productsUrl,
                'badge' => 'Premium',
                'grade' => 'Raw Makhana',
            ],
            [
                'title' => 'Premium Makhana',
                'description' => 'Large, clean and carefully graded Makhana suitable for premium retail and food brands.',
                'image' => 'assests/img/hq-white.jpg',
                'url' => $productsUrl,
                'badge' => 'Best Seller',
                'grade' => 'Premium Grade',
            ],
            [
                'title' => 'Roasted Makhana',
                'description' => 'Lightly roasted Makhana with a delicious crunch, perfect for healthy snacking.',
                'image' => 'assests/img/hq-roasted.jpg',
                'url' => $productsUrl,
                'badge' => 'Ready to Eat',
                'grade' => 'Roasted',
            ],
            [
                'title' => 'Flavoured Makhana',
                'description' => 'Deliciously seasoned Makhana available in a range of exciting flavours.',
                'image' => 'assests/img/hq-masala.jpg',
                'url' => $productsUrl,
                'badge' => 'Flavoured',
                'grade' => 'Flavoured',
            ],
            [
                'title' => 'Bulk Makhana',
                'description' => 'Reliable bulk Makhana supply for distributors, wholesalers, retailers and food manufacturers.',
                'image' => 'assests/img/hq-grains-2.jpg',
                'url' => $productsUrl,
                'badge' => 'Wholesale',
                'grade' => 'Bulk Supply',
            ],
            [
                'title' => 'Private Label Makhana',
                'description' => 'Build your own Makhana brand with our sourcing, processing and packaging solutions.',
                'image' => 'assests/img/hq-bowl.jpg',
                'url' => $productsUrl,
                'badge' => 'Custom',
                'grade' => 'Private Label',
            ],
        ];
    }

    /**
     * @return Collection<int, array{title: string, description: string, image_url: string, url: string, badge: ?string, grade: string, enquiry_url: string}>
     */
    public static function defaults(): Collection
    {
        return collect(self::items())
            ->map(fn (array $item): array => self::normalize($item))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $product
     * @return array{title: string, description: string, image_url: string, url: string, badge: ?string, grade: string, enquiry_url: string}
     */
    public static function normalize(array $product): array
    {
        $title = trim((string) ($product['title'] ?? ''));
        $catalog = self::matchCatalogItem($title);

        $resolvedTitle = $title !== '' ? $title : (string) ($catalog['title'] ?? 'Premium Makhana');
        $image = trim((string) ($product['image'] ?? $catalog['image'] ?? 'assests/img/hq-bowl.jpg'));

        return [
            'title' => $resolvedTitle,
            'description' => trim((string) ($product['description'] ?? $catalog['description'] ?? 'Premium fox nuts sourced from Mithilanchal, Bihar.')),
            'image_url' => self::imageUrl($image !== '' ? $image : 'assests/img/hq-bowl.jpg'),
            'url' => filled($product['url'] ?? null) ? (string) $product['url'] : (string) ($catalog['url'] ?? route('Product')),
            'badge' => filled($product['badge'] ?? null) ? (string) $product['badge'] : ($catalog['badge'] ?? null),
            'grade' => filled($product['grade'] ?? null) ? (string) $product['grade'] : (string) ($catalog['grade'] ?? 'Makhana'),
            'enquiry_url' => route('ContactUs', ['product' => $resolvedTitle]).'#contact-form',
        ];
    }

    public static function imageUrl(?string $image): string
    {
        if (blank($image)) {
            return asset('assests/img/hq-bowl.jpg');
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        return asset(ltrim($image, '/'));
    }

    public static function requirementFor(?string $productTitle): ?string
    {
        if (blank($productTitle)) {
            return null;
        }

        $title = Str::lower($productTitle);

        foreach (ContactLead::REQUIREMENTS as $requirement) {
            if (str_contains($title, Str::lower($requirement))) {
                return $requirement;
            }
        }

        return match (true) {
            str_contains($title, 'bulk') || str_contains($title, 'wholesale') => 'Bulk Wholesale Makhana',
            str_contains($title, 'roast') => 'Roasted Makhana',
            str_contains($title, 'flavour') || str_contains($title, 'flavor') || str_contains($title, 'masala') => 'Flavoured Makhana',
            str_contains($title, 'private') || str_contains($title, 'label') => 'Private Label',
            str_contains($title, 'export') => 'Export Enquiry',
            str_contains($title, 'premium') || str_contains($title, 'raw') => 'Premium Makhana',
            default => 'Other',
        };
    }

    /**
     * @return array{title: string, description: string, image: string, url: string, badge: string, grade: string}|array{}
     */
    private static function matchCatalogItem(string $title): array
    {
        if ($title === '') {
            return [];
        }

        $needle = Str::lower($title);

        return collect(self::items())->first(function (array $item) use ($needle): bool {
            $catalogTitle = Str::lower($item['title']);

            return $catalogTitle === $needle
                || str_contains($catalogTitle, $needle)
                || str_contains($needle, $catalogTitle);
        }) ?? [];
    }
}
