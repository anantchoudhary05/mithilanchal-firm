<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use MoonShine\Laravel\MoonShineAuth;

class AdminLogoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        MoonShineAuth::getGuard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('moonshine.login');
    }
}
