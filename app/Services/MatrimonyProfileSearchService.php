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
     * Default an unfiltered search to the current matrimony user's opposite gender.
     */
    public function applyDefaultOppositeGender(Builder $query, Request $request, ?User $user): void
    {
        if (! $user || $this->hasSelectedFilter($request)) {
            return;
        }

        $user->loadMissing('matrimonyProfile');
        $gender = strtolower((string) data_get($user->matrimonyProfile?->personal_details, 'gender'));
        $oppositeGender = match ($gender) {
            'male' => 'female',
            'female' => 'male',
            default => null,
        };

        if (! $oppositeGender) {
            return;
        }

        $query->whereIn('personal_details->gender', [
            $oppositeGender,
            ucfirst($oppositeGender),
            strtoupper($oppositeGender),
        ]);
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
