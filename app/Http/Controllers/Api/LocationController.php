<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Locality;
use App\Models\State;
use App\Models\Taluka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function states()
    {
        return response()->json(['success' => true, 'data' => State::query()
            ->where('is_active', true)->where('external_code', '!=', 'LEGACY')->orderBy('name')
            ->get()->map(fn (State $state) => ['state_key' => $state->external_code, 'name' => $state->name])]);
    }

    public function districts(Request $request)
    {
        $data = $request->validate(['state_key' => 'required|string|exists:states,external_code']);
        $rows = District::query()->where('is_active', true)
            ->whereHas('state', fn ($q) => $q->where('external_code', $data['state_key']))
            ->with('state')->orderBy('name')->get()
            ->map(fn (District $district) => [
                'state_key' => $district->state->external_code,
                'district_key' => $district->external_code,
                'name' => $district->name,
            ]);

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function talukas(Request $request)
    {
        $data = $request->validate([
            'state_key' => 'required|string|exists:states,external_code',
            'district_key' => 'required|string|exists:districts,external_code',
        ]);
        $rows = Taluka::query()->where('is_active', true)
            ->whereHas('district', fn ($q) => $q->where('external_code', $data['district_key'])
                ->whereHas('state', fn ($state) => $state->where('external_code', $data['state_key'])))
            ->with('district.state')->orderBy('name')->get()
            ->map(fn (Taluka $taluka) => [
                'state_key' => $taluka->district->state->external_code,
                'district_key' => $taluka->district->external_code,
                'taluka_key' => $taluka->external_code,
                'name' => $taluka->name,
            ]);

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function cities(Request $request)
    {
        $data = $request->validate([
            'state_key' => 'required|string|exists:states,external_code',
            'district_key' => 'required|string|exists:districts,external_code',
            'taluka_key' => 'nullable|string|exists:talukas,external_code',
        ]);
        $rows = Locality::query()->where('is_active', true)->where('type', 'city')
            ->whereHas('taluka.district', fn ($q) => $q->where('external_code', $data['district_key'])
                ->whereHas('state', fn ($state) => $state->where('external_code', $data['state_key'])))
            ->when($data['taluka_key'] ?? null, fn ($q, $key) => $q->whereHas('taluka', fn ($taluka) => $taluka->where('external_code', $key)))
            ->with('taluka.district.state')->orderBy('name')->get()
            ->map(fn (Locality $locality) => [
                'state_key' => $locality->taluka->district->state->external_code,
                'district_key' => $locality->taluka->district->external_code,
                'taluka_key' => $locality->taluka->external_code,
                'city_key' => $locality->external_code,
                'name' => $locality->name,
            ]);

        return response()->json(['success' => true, 'data' => $rows]);
    }

    /**
     * Get location details by country code and pin code
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocationByPinCode(Request $request)
    {
        // Validate the input parameters
        $validated = $request->validate([
            'country_code' => 'required|string|max:2',
            'pin_code' => 'required|string|max:10',
        ]);

        try {
            // Make request to zippopotam.us API
            $country_code = strtolower($validated['country_code']);
            $pin_code = $validated['pin_code'];

            $response = Http::get("https://api.zippopotam.us/{$country_code}/{$pin_code}");

            // Check if the external API returned a successful response
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found for the given country code and pin code.',
                    'status' => $response->status(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching location data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
