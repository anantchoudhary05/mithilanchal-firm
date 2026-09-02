<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\CmsUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class TinyMceImageUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        abort_unless(CmsUser::check() && (CmsUser::isAdmin() || CmsUser::isAuthor()), 403);

        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $path = $request->file('file')->store('blogs/content', 'public');

        return response()->json([
            'location' => Storage::disk('public')->url($path),
        ]);
    }
}
