<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineAuth;

final class CmsUser
{
    public static function user(): ?MoonshineUser
    {
        $user = MoonShineAuth::getGuard()->user();

        if ($user instanceof MoonshineUser) {
            return $user;
        }

        if ($user === null) {
            return null;
        }

        return MoonshineUser::query()->find($user->getAuthIdentifier());
    }

    public static function id(): ?int
    {
        $id = MoonShineAuth::getGuard()->id();

        return $id !== null ? (int) $id : null;
    }

    public static function check(): bool
    {
        return MoonShineAuth::getGuard()->check();
    }

    public static function isAdmin(): bool
    {
        $user = self::user();

        if ($user === null) {
            return false;
        }

        return $user->isAdmin();
    }

    public static function isAuthor(): bool
    {
        $user = self::user();

        if ($user === null) {
            return false;
        }

        return $user->isAuthor();
    }

    public static function matchesSelectedRole(string $role): bool
    {
        $user = self::user();

        if ($user === null) {
            return false;
        }

        return match ($role) {
            CmsRole::LOGIN_ADMIN => $user->isAdmin(),
            CmsRole::LOGIN_AUTHOR => $user->isAuthor(),
            default => false,
        };
    }
}
