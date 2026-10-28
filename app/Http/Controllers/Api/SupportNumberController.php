<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;

class SupportNumberController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ['support_numbers' => [
                ['key' => 'primary', 'label' => 'Primary Support Number', 'number' => SystemSetting::getValue('support_number_primary', '')],
                ['key' => 'secondary', 'label' => 'Secondary Support Number', 'number' => SystemSetting::getValue('support_number_secondary', '')],
            ]],
        ]);
    }
}
