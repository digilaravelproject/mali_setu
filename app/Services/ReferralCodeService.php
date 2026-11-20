<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralCodeService
{
    public function generate(User $user, ?string $talukaOverride = null): string
    {
        $base = $this->baseFor($user, $talukaOverride);
        $candidate = $base;
        $suffix = 2;

        while (User::query()->where('reffral_code', $candidate)
            ->when($user->exists, fn ($query) => $query->whereKeyNot($user->getKey()))
            ->exists()) {
            $candidate = Str::limit($base, 50 - strlen((string) $suffix), '').$suffix;
            $suffix++;
        }

        return $candidate;
    }

    public function refresh(User $user, ?string $talukaOverride = null): string
    {
        $code = $this->generate($user, $talukaOverride);
        $user->forceFill(['reffral_code' => $code])->saveQuietly();

        return $code;
    }

    public function refreshAll(): int
    {
        return DB::transaction(function () {
            User::query()->select('id')->orderBy('id')->chunkById(500, function ($users) {
                foreach ($users as $user) {
                    $user->forceFill(['reffral_code' => 'TEMPREF'.$user->id])->saveQuietly();
                }
            });

            $updated = 0;
            User::query()->with(['volunteerReferralProfile.taluka', 'business', 'matrimonyProfile'])
                ->orderBy('id')->chunkById(200, function ($users) use (&$updated) {
                    foreach ($users as $user) {
                        $this->refresh($user);
                        $updated++;
                    }
                });

            return $updated;
        });
    }

    public function baseFor(User $user, ?string $talukaOverride = null): string
    {
        $firstName = $user->first_name ?: $this->extractFirstName((string) $user->name);
        $taluka = $talukaOverride ?: $user->taluka;

        if (! $taluka && $user->exists) {
            $taluka = $user->volunteerReferralProfile?->taluka?->name
                ?: data_get($user->matrimonyProfile?->location_details, 'taluka')
                ?: $user->business?->taluka;
        }

        // Older web registrations did not persist their taluka and commonly
        // stored that value in district, so prefer district before city.
        $taluka = $taluka ?: $user->district ?: $user->city ?: 'local';
        $date = $user->dob ?: $user->created_at ?: now();
        try {
            $dayMonth = Carbon::parse($date)->format('dm');
        } catch (\Throwable) {
            $dayMonth = now()->format('dm');
        }

        return Str::limit(
            $this->normalize($firstName ?: 'member').$this->normalize($taluka).$dayMonth,
            50,
            ''
        );
    }

    public function extractFirstName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $titles = ['mr', 'mr.', 'mrs', 'mrs.', 'ms', 'ms.', 'dr', 'dr.', 'shri', 'smt'];
        while ($parts && in_array(Str::lower($parts[0]), $titles, true)) {
            array_shift($parts);
        }

        return (string) ($parts[0] ?? 'member');
    }

    private function normalize(string $value): string
    {
        return Str::lower(preg_replace('/[^a-zA-Z0-9]/', '', Str::ascii($value)) ?: '');
    }
}
