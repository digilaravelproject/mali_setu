<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomepageHeroResource;
use App\Models\HomepageHero;
use App\Models\Webinar;

class HomepageHeroController extends Controller
{
    // GET /api/heroes
    public function index()
    {
        $heroes = HomepageHero::latest()->get();

        return response()->json([
            'data' => HomepageHeroResource::collection($heroes)->resolve(),
            'upcoming_webinars' => Webinar::where('status', 'published')
                ->where('starts_at', '>', now())->withCount('registrations')
                ->orderBy('starts_at')->limit(3)->get(),
        ]);
    }

    // GET /api/heroes/{hero}
    public function show(HomepageHero $hero)
    {
        return new HomepageHeroResource($hero);
    }
}
