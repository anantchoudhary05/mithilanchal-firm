<?php

declare(strict_types=1);

namespace App\MoonShine\Auth;

use App\Support\CmsRole;
use App\Support\CmsUser;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use MoonShine\Laravel\Http\Requests\LoginFormRequest;
use MoonShine\Laravel\MoonShineAuth;

final class LoginWithSelectedRole
{
    public function handle(LoginFormRequest $request, Closure $next): RedirectResponse
    {
        $role = (string) $request->input('role', '');

        if (! CmsRole::isLoginKey($role)) {
            throw ValidationException::withMessages([
                'role' => 'Select whether you are logging in as Admin or Author.',
            ]);
        }

        $request->authenticate();

        if (! CmsUser::matchesSelectedRole($role)) {
            MoonShineAuth::getGuard()->logout();

            throw ValidationException::withMessages([
                'role' => 'This account does not match the selected role. Choose Admin or Author as assigned to this user.',
            ]);
        }

        return redirect()->intended(
            moonshineRouter()->getEndpoints()->home()
        );
    }
}
