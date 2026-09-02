<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\CmsRole;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MoonShine\Laravel\Models\MoonshineUser as BaseMoonshineUser;

class MoonshineUser extends BaseMoonshineUser
{
    protected $fillable = [
        'email',
        'moonshine_user_role_id',
        'password',
        'name',
        'avatar',
        'bio',
    ];

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'author_id');
    }

    public function isAdmin(): bool
    {
        return $this->isSuperUser()
            || strcasecmp((string) $this->moonshineUserRole?->name, CmsRole::ADMIN_NAME) === 0;
    }

    public function isAuthor(): bool
    {
        return (int) $this->moonshine_user_role_id === CmsRole::authorId()
            || strcasecmp((string) $this->moonshineUserRole?->name, CmsRole::AUTHOR_NAME) === 0;
    }
}
