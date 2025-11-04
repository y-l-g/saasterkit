<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Requests\Auth\AppearanceUpdateRequest;
use Illuminate\Http\RedirectResponse;

final readonly class AppearanceUpdateController
{
    public function __invoke(AppearanceUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated())->save();

        return back()->with('success', 'Appearance settings updated successfully.');
    }
}
