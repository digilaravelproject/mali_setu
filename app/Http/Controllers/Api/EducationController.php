<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Get list of educations (paginated). Query params: search, per_page
     */
    public function index(Request $request)
    {
        $educations = Education::query()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $educations,
        ]);
    }
}
