<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomepageHeroResource;
use App\Models\HomepageHero;

class HomepageHeroController extends Controller
{
    // GET /api/heroes
    public function index()
    {
        $heroes = HomepageHero::latest()->get();

        return HomepageHeroResource::collection($heroes);
    }

    // GET /api/heroes/{hero}
    public function show(HomepageHero $hero)
    {
        return new HomepageHeroResource($hero);
    }
}
