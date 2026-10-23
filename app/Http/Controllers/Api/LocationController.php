<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
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
