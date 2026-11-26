<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SettingKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the foundation's details.
     */
    public function edit(SiteSettings $settings): View
    {
        return view('admin.settings.edit', [
            'groups' => collect(SettingKey::cases())->groupBy(fn (SettingKey $key) => $key->group()),
            'settings' => $settings,
        ]);
    }

    /**
     * Update the foundation's details.
     */
    public function update(UpdateSettingsRequest $request, SiteSettings $settings): RedirectResponse
    {
        $settings->update(array_map(fn (?string $value) => $value ?? '', $request->validated()));

        return to_route('admin.settings.edit')->with('notice', 'De gegevens zijn opgeslagen.');
    }
}
