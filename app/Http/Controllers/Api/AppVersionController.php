<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\JsonResponse;

class AppVersionController extends Controller
{
    public function index(): JsonResponse
    {
        $versions = AppVersion::query()
            ->whereIn('platform', ['android', 'ios', 'apple'])
            ->get()
            ->keyBy('platform');

        $iosVersion = $versions->get('ios') ?? $versions->get('apple');

        return response()->json([
            'success' => true,
            'message' => 'App version information retrieved successfully.',
            'data' => [
                'android' => $this->formatVersion($versions->get('android')),
                'ios' => $this->formatVersion($iosVersion),
                'app_update_url' => url('/app_update'),
            ],
        ]);
    }

    private function formatVersion(?AppVersion $version): ?array
    {
        if (! $version) {
            return null;
        }

        return [
            'version' => $version->version,
            'build_code' => $version->build_code,
            'min_version' => $version->min_version,
            'min_build' => $version->min_build,
            'store_url' => $version->store_url,
            'update_notes' => $version->update_notes,
            'updated_at' => $version->updated_at?->toIso8601String(),
        ];
    }
}
