<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function volunteer(string $code, ReferralService $referrals): JsonResponse
    {
        $user = $referrals->findUserByCode($code);

        return response()->json([
            'success' => true,
            'data' => $referrals->volunteerSummary($user),
        ]);
    }

    public function shareLink(string $code, ReferralService $referrals): JsonResponse
    {
        $user = $referrals->findUserByCode($code);
        $link = $referrals->shareLink($user);

        return response()->json([
            'success' => true,
            'data' => [
                'referral_code' => $user->reffral_code,
                'referral_link' => $link,
                'share_message' => "Join Mali Setu using my referral link: {$link}",
            ],
        ]);
    }

    public function referredUsers(Request $request, string $code, ReferralService $referrals): JsonResponse
    {
        $user = $this->authorizedOwner($request, $code, $referrals);
        $validated = $request->validate([
            'user_type' => ['nullable', 'in:general,business,matrimony,volunteer'],
            'status' => ['nullable', 'in:active,inactive'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = $user->referralsMade()->with('referredUser:id,name,email,phone,user_type,status,photo,district,city,village,created_at');
        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }
        if (! empty($validated['user_type'])) {
            $query->whereHas('referredUser', fn ($q) => $q->where('user_type', $validated['user_type']));
        }

        $page = $query->latest()->paginate($validated['per_page'] ?? 20);

        return response()->json([
            'success' => true,
            'data' => collect($page->items())->map(fn (Referral $referral) => $this->referralData($referral))->values(),
            'meta' => $this->paginationMeta($page),
        ]);
    }

    public function history(Request $request, string $code, ReferralService $referrals): JsonResponse
    {
        $user = $this->authorizedOwner($request, $code, $referrals);
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = $user->referralsMade()->with('referredUser:id,name,phone,user_type,status,photo,district,city,village,created_at');
        if (! empty($validated['from'])) {
            $query->whereDate('created_at', '>=', $validated['from']);
        }
        if (! empty($validated['to'])) {
            $query->whereDate('created_at', '<=', $validated['to']);
        }
        $page = $query->latest()->paginate($validated['per_page'] ?? 20);

        return response()->json([
            'success' => true,
            'data' => collect($page->items())->map(fn (Referral $referral) => $this->referralData($referral))->values(),
            'meta' => $this->paginationMeta($page),
        ]);
    }

    public function network(Request $request, string $code, ReferralService $referrals): JsonResponse
    {
        $user = $this->authorizedOwner($request, $code, $referrals);
        $validated = $request->validate([
            'depth' => ['nullable', 'integer', 'min:1', 'max:5'],
            'district' => ['nullable', 'string', 'max:100'],
            'taluka' => ['nullable', 'string', 'max:100'],
            'city_village' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $referrals->network($user, $validated['depth'] ?? 3, $validated),
        ]);
    }

    public function earnings(Request $request, string $code, ReferralService $referrals): JsonResponse
    {
        $user = $this->authorizedOwner($request, $code, $referrals);
        $active = $user->referralsMade()->where('status', 'active');
        $profile = $user->volunteerReferralProfile;

        return response()->json([
            'success' => true,
            'data' => [
                'volunteer' => $referrals->volunteerSummary($user),
                'commission_points_per_referral' => $profile?->commission_points ?? 0,
                'total_referrals' => $user->referralsMade()->count(),
                'active_referrals' => (clone $active)->count(),
                'total_earned_points' => (int) (clone $active)->sum('commission_points'),
                'pending_points' => (int) $user->referralsMade()->where('status', 'inactive')->sum('commission_points'),
            ],
        ]);
    }

    private function authorizedOwner(Request $request, string $code, ReferralService $referrals): User
    {
        $owner = $referrals->findUserByCode($code);
        $actor = $request->user();
        abort_unless($actor && ($actor->is($owner) || $actor->user_type === 'admin'), 403, 'You may only access referral details for your own code.');

        return $owner;
    }

    private function referralData(Referral $referral): array
    {
        $member = $referral->referredUser;

        return [
            'id' => $referral->id,
            'referral_code_used' => $referral->referral_code_used,
            'commission_points' => $referral->commission_points,
            'status' => $referral->status,
            'referred_at' => $referral->created_at?->toISOString(),
            'user' => $member ? [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone,
                'user_type' => $member->user_type,
                'status' => $member->status,
                'photo_url' => $member->photo ? asset('storage/'.$member->photo) : asset('default-avatar.png'),
                'location' => collect([$member->district, $member->city ?: $member->village])->filter()->implode(', '),
                'registered_at' => $member->created_at?->toISOString(),
            ] : null,
        ];
    }

    private function paginationMeta($page): array
    {
        return [
            'current_page' => $page->currentPage(),
            'last_page' => $page->lastPage(),
            'per_page' => $page->perPage(),
            'total' => $page->total(),
        ];
    }
}
