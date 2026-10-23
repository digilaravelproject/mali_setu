<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessPlan;
use App\Models\MatrimonyPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Return active business plans
     */
    public function businessPlans(Request $request)
    {
        $plans = BusinessPlan::where('active', true)
            // ->orderByRaw("FIELD(company_type, 'Proprietary(Owner)/Partnership - LLP', 'Private Ltd') DESC")
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['plans' => $plans]
        ]);
    }

    /**
     * Return active matrimony plans
     */
    public function matrimonyPlans(Request $request)
    {
        $plans = MatrimonyPlan::where('active', true)
            ->orderBy('duration_years')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['plans' => $plans]
        ]);
    }
}
