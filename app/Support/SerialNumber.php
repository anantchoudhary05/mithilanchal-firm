<?php

declare(strict_types=1);

namespace App\Support;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\Preview;

final class SerialNumber
{
    public static function field(int $offset = 0): Preview
    {
        return Preview::make(
            'S.No.',
            formatted: static fn (mixed $item, int $index): int => $offset + $index + 1,
        )->columnSelection(false);
    }

    public static function forIndexPage(IndexPage $page, int $perPage = 25): Preview
    {
        $pageNumber = max(1, (int) $page->getResource()->getQueryParam('page', 1));

        return self::field(($pageNumber - 1) * $perPage);
    }
}
