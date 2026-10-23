<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    // Add this line
    protected $table = 'educations';

    protected $fillable = [
        'user_id',
        'highest_qualification',
        'college',
        'university',
        'specialization',
        'passing_year',
        'percentage',
        'description',
        'is_active',
    ];

    protected $casts = [
        'passing_year' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
