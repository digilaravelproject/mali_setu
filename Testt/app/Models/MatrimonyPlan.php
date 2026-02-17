<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatrimonyPlan extends Model
{
    protected $fillable = [
        'plan_name',
        'duration_years',
        'price',
        'active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean'
    ];
}
