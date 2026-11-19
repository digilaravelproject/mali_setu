<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessPlan extends Model
{
    /**
     * Normalize legacy company-type labels so plans created with harmless
     * punctuation/spacing differences still match existing businesses.
     */
    public static function normalizeCompanyType(?string $companyType): string
    {
        $normalized = mb_strtolower($companyType ?? '');
        $normalized = preg_replace('/\bowner\b/u', '', $normalized);

        return preg_replace('/[^a-z0-9]+/u', '', $normalized) ?? '';
    }

    protected $fillable = [
        'company_type',
        'duration_years',
        'price',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];
}
