<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppVersionController extends Controller
{
    public function index(): View
    {
        $versions = AppVersion::query()
            ->whereIn('platform', ['android', 'ios', 'apple'])
            ->get()
            ->keyBy('platform');

        // Older/manual records may use "apple" instead of the canonical "ios".
        if (! $versions->has('ios') && $versions->has('apple')) {
            $versions->put('ios', $versions->get('apple'));
        }

        return view('admin.app-versions.index', compact('versions'));
    }

    public function update(Request $request, AppVersion $appVersion): RedirectResponse
    {
        $validated = $request->validate([
            'version' => ['required', 'string', 'max:50', 'regex:/^\d+(\.\d+){1,3}([\-+][0-9A-Za-z.-]+)?$/'],
            'build_code' => ['required', 'integer', 'min:1'],
            'min_version' => ['required', 'string', 'max:50', 'regex:/^\d+(\.\d+){1,3}([\-+][0-9A-Za-z.-]+)?$/'],
            'min_build' => ['required', 'integer', 'min:1'],
            'store_url' => ['required', 'url:http,https', 'max:2048'],
            'update_notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'version.regex' => 'The version must look like 1.0.0.',
            'min_version.regex' => 'The minimum version must look like 1.0.0.',
        ]);

        $appVersion->update($validated);

        return redirect()
            ->route('admin.app-versions.index')
            ->with('success', ucfirst($appVersion->platform).' app information updated successfully.');
    }
}
