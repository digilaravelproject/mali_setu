<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use App\Models\VolunteerReferralProfile;
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
}
