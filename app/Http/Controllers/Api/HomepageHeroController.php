<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomepageHeroResource;
use App\Models\HomepageHero;
use Illuminate\Http\Request;

class HomepageHeroController extends Controller
{
    // GET /api/heroes?limit=10&page=1
    public function index(Request $request)
    {
        
        // optional limit (default 10). If you want "all", pass limit=0
        $limit = (int) $request->get('limit', 10);

        if ($limit === 0) {
            $heroes = HomepageHero::latest()->get();
            return HomepageHeroResource::collection($heroes);
        }

        $heroes = HomepageHero::latest()->paginate($limit)->appends($request->query());

        return HomepageHeroResource::collection($heroes)
            ->additional([
                'meta' => [
                    'current_page' => $heroes->currentPage(),
                    'per_page'     => $heroes->perPage(),
                    'total'        => $heroes->total(),
                    'last_page'    => $heroes->lastPage(),
                ],
            ]);
    }

    // GET /api/heroes/{hero}
    public function show(HomepageHero $hero)
    {
        return new HomepageHeroResource($hero);
    }
}
