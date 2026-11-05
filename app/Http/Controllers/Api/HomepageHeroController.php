<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomepageHeroResource;
use App\Models\HomepageHero;
use App\Models\User;
use App\Models\Webinar;

class HomepageHeroController extends Controller
{
    // GET /api/heroes
    public function index()
    {
        $heroes = HomepageHero::latest()->get();

        return response()->json([
            'data' => HomepageHeroResource::collection($heroes)->resolve(),
            'leaderboard' => User::query()
                ->whereHas('volunteer', fn ($q) => $q->where('status', 'active'))
                ->withCount(['referralsMade as referrals_count' => fn ($q) => $q->where('status', 'active')])
                ->withSum(['referralsMade as earned_points' => fn ($q) => $q->where('status', 'active')], 'commission_points')
                ->orderByDesc('referrals_count')
                ->orderBy('name')
                ->limit(3)
                ->get(['id', 'name', 'photo', 'reffral_code'])
                ->values()
                ->map(fn (User $user, int $index) => [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'name' => $user->name,
                    'referral_code' => $user->reffral_code,
                    'photo_url' => $user->photo ? asset('storage/'.$user->photo) : asset('default-avatar.png'),
                    'total_referrals' => (int) $user->referrals_count,
                    'total_earned_points' => (int) ($user->earned_points ?? 0),
                ]),
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
