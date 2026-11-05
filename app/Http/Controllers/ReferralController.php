<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request, ReferralService $referrals)
    {
        $user = $request->user()->load([
            'volunteer',
            'volunteerReferralProfile.district',
            'volunteerReferralProfile.taluka',
            'volunteerReferralProfile.locality',
        ]);
        $history = $user->referralsMade()->with('referredUser')->latest()->paginate(20);
        $summary = $referrals->volunteerSummary($user);
        $network = $referrals->network($user, 3);

        return view('referrals.index', compact('user', 'history', 'summary', 'network'));
    }
}
