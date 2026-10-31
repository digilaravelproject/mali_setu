<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminTeamMember extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'first_name',
        'last_name',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->is_active && in_array($permission, $this->permissions ?? [], true);
    }
}
