<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use App\Models\VolunteerReferralProfile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class ReferralService
{
    public function generateUniqueCode(): string
    {
        do {
            $code = 'MSU'.strtoupper(Str::random(7));
        } while (User::where('reffral_code', $code)->exists());

        return $code;
    }

    public function ensureCode(User $user): string
    {
        if (! $user->reffral_code) {
            $user->forceFill(['reffral_code' => $this->generateUniqueCode()])->saveQuietly();
        }

        return $user->reffral_code;
    }

    public function record(?string $submittedCode, User $referredUser): ?Referral
    {
        $submittedCode = strtoupper(trim((string) $submittedCode));
        if ($submittedCode === '') {
            return null;
        }

        $referrer = User::whereRaw('UPPER(reffral_code) = ?', [$submittedCode])->first();
        if (! $referrer || $referrer->is($referredUser)) {
            return null;
        }

        $referredUser->forceFill(['referred_by_user_id' => $referrer->id])->saveQuietly();
        $profile = $referrer->volunteerReferralProfile;

        return Referral::updateOrCreate(
            ['referred_user_id' => $referredUser->id],
            [
                'referrer_user_id' => $referrer->id,
                'referral_code_used' => $submittedCode,
                'commission_points' => $profile?->commission_points ?? 0,
                'status' => $profile && ! $profile->is_active ? 'inactive' : 'active',
            ]
        );
    }

    public function activateVolunteer(User $user): VolunteerReferralProfile
    {
        return VolunteerReferralProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['commission_points' => 2, 'is_active' => true]
        );
    }

    public function findUserByCode(string $code): User
    {
        $user = User::query()
            ->with(['volunteer', 'volunteerReferralProfile.district', 'volunteerReferralProfile.taluka', 'volunteerReferralProfile.locality'])
            ->whereRaw('UPPER(reffral_code) = ?', [strtoupper(trim($code))])
            ->first();

        if (! $user) {
            throw (new ModelNotFoundException)->setModel(User::class, [$code]);
        }

        return $user;
    }

    public function shareLink(User $user): string
    {
        return route('register', ['referral_code' => $this->ensureCode($user)]);
    }

    public function volunteerSummary(User $user): array
    {
        $profile = $user->volunteerReferralProfile;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'referral_code' => $this->ensureCode($user),
            'referral_link' => $this->shareLink($user),
            'photo_url' => $user->photo ? asset('storage/'.$user->photo) : asset('default-avatar.png'),
            'user_type' => $user->user_type,
            'is_volunteer' => (bool) $user->volunteer,
            'volunteer_status' => $user->volunteer?->status,
            'commission_points' => $profile?->commission_points ?? 0,
            'total_referrals' => $user->referralsMade()->count(),
            'total_earned_points' => (int) $user->referralsMade()->where('status', 'active')->sum('commission_points'),
            'location' => [
                'district' => $profile?->district?->name,
                'taluka' => $profile?->taluka?->name,
                'city_village' => $profile?->locality?->name,
                'city_village_type' => $profile?->locality?->type,
            ],
        ];
    }

    public function network(User $root, int $depth, array $filters = []): array
    {
        $build = function (User $user, int $remaining, int $level) use (&$build, $filters): array {
            $query = $user->referralsMade()->with(['referredUser.volunteerReferralProfile.district', 'referredUser.volunteerReferralProfile.taluka', 'referredUser.volunteerReferralProfile.locality']);

            $children = $query->get()->map(function (Referral $referral) use (&$build, $remaining, $level, $filters) {
                $member = $referral->referredUser;
                if (! $member) {
                    return null;
                }
                $location = $member->volunteerReferralProfile;
                $matches = (! isset($filters['district']) || strcasecmp((string) $location?->district?->name, $filters['district']) === 0)
                    && (! isset($filters['taluka']) || strcasecmp((string) $location?->taluka?->name, $filters['taluka']) === 0)
                    && (! isset($filters['city_village']) || strcasecmp((string) $location?->locality?->name, $filters['city_village']) === 0);

                $node = [
                    'user_id' => $member->id,
                    'name' => $member->name,
                    'referral_code' => $member->reffral_code,
                    'user_type' => $member->user_type,
                    'level' => $level,
                    'commission_points' => $referral->commission_points,
                    'location' => [
                        'district' => $location?->district?->name,
                        'taluka' => $location?->taluka?->name,
                        'city_village' => $location?->locality?->name,
                    ],
                    'children' => $remaining > 1 ? $build($member, $remaining - 1, $level + 1)['children'] : [],
                ];

                return $matches || $node['children'] !== [] ? $node : null;
            })->filter()->values()->all();

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'referral_code' => $user->reffral_code,
                'level' => $level - 1,
                'children' => $children,
            ];
        };

        return $build($root, $depth, 1);
    }
}
