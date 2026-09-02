<?php

declare(strict_types=1);

namespace App\Support;

use MoonShine\Laravel\Models\MoonshineUserRole;

final class CmsRole
{
    public const int ADMIN = MoonshineUserRole::DEFAULT_ROLE_ID;

    public const string AUTHOR_NAME = 'Author';

    public const string ADMIN_NAME = 'Admin';

    public const string LOGIN_ADMIN = 'admin';

    public const string LOGIN_AUTHOR = 'author';

    /**
     * @return array<string, string>
     */
    public static function loginOptions(): array
    {
        return [
            self::LOGIN_ADMIN => self::ADMIN_NAME,
            self::LOGIN_AUTHOR => self::AUTHOR_NAME,
        ];
    }

    public static function isLoginKey(string $role): bool
    {
        return array_key_exists($role, self::loginOptions());
    }

    public static function authorId(): int
    {
        $id = MoonshineUserRole::query()
            ->where('name', self::AUTHOR_NAME)
            ->value('id');

        return $id !== null ? (int) $id : 2;
    }
}
