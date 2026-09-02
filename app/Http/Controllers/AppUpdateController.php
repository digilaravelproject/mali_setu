<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppUpdateController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        $platform = preg_match('/iphone|ipad|ipod|macintosh|mac os x/', $userAgent)
            ? 'ios'
            : 'android';

        $platformNames = $platform === 'ios' ? ['ios', 'apple'] : ['android'];
        $storeUrl = AppVersion::query()
            ->whereIn('platform', $platformNames)
            ->orderByRaw('CASE WHEN platform = ? THEN 0 ELSE 1 END', [$platform])
            ->value('store_url');

        abort_unless($storeUrl, 404, 'App store URL is not configured.');

        return redirect()->away($storeUrl);
    }
}
