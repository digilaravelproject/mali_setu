<?php

namespace App\Services;

use App\Models\MatrimonyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MatrimonyProfileSearchService
{
    private const FILTER_FIELDS = [
        'name',
        'gender',
        'age_min',
        'age_max',
        'height',
        'height_min',
        'height_max',
        'profile_created_by',
        'marital_status',
        'language',
        'physical_status',
        'annual_income',
        'education',
        'employment_type',
        'occupation',
        'manglik',
        'dosh',
        'family_status',
        'family_type',
        'family_value',
        'family_class',
        'country',
        'state',
        'city',
        'location',
        'citizenship',
        'caste',
        'diet',
        'smoking',
        'drinking',
        'photo',
        'created_at',
    ];

    /**
     * Default a matrimony search to the current user's opposite gender
     * unless an explicit gender filter is provided.
     */
    public function applyDefaultOppositeGender(Builder $query, Request $request, ?User $user): void
    {
        if (! $user || $this->hasSelectedGenderFilter($request)) {
            return;
        }

        $user->loadMissing('matrimonyProfile');

        $gender = null;
        if ($user->matrimonyProfile) {
            $gender = data_get($user->matrimonyProfile->personal_details, 'gender')
                ?? $user->matrimonyProfile->gender
                ?? null;
        }
        if (! $gender && isset($user->gender)) {
            $gender = $user->gender;
        }

        $genderStr = strtolower(trim((string) $gender));
        $oppositeGender = match ($genderStr) {
            'male' => 'female',
            'female' => 'male',
            default => null,
        };

        if (! $oppositeGender) {
            return;
        }

        $query->where(function (Builder $q) use ($oppositeGender) {
            $q->whereIn('personal_details->gender', [
                $oppositeGender,
                ucfirst($oppositeGender),
                strtoupper($oppositeGender),
            ]);

            $driver = $q->getConnection()->getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                $q->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(personal_details, "$.gender"))) = ?', [$oppositeGender]);
            }
        });
    }

    public function hasSelectedGenderFilter(Request $request): bool
    {
        if ($request->filled('gender')) {
            $value = strtolower(trim((string) $request->input('gender')));

            return ! in_array($value, ['', 'any', 'all', "doesn't matter", 'doesnt matter'], true);
        }

        return false;
    }

    public function hasSelectedFilter(Request $request): bool
    {
        foreach (self::FILTER_FIELDS as $field) {
            if ($this->isMeaningfulValue($request->input($field))) {
                return true;
            }
        }

        return false;
    }

    private function isMeaningfulValue(mixed $value): bool
    {
        if (is_array($value)) {
            return collect($value)->contains(fn ($item) => $this->isMeaningfulValue($item));
        }

        if ($value === null) {
            return false;
        }

        $normalized = strtolower(trim((string) $value));

        return ! in_array($normalized, ['', 'any', 'all', "doesn't matter", 'doesnt matter'], true);
    }
}
