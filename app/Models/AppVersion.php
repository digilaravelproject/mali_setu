<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $fillable = [
        'platform',
        'version',
        'build_code',
        'min_version',
        'min_build',
        'store_url',
        'update_notes',
    ];

    protected function casts(): array
    {
        return [
            'build_code' => 'integer',
            'min_build' => 'integer',
        ];
    }
}
