<?php

namespace App\Services;

use App\Models\MatrimonyBlock;
use App\Models\MatrimonyReport;
use Illuminate\Database\Eloquent\Builder;

class MatrimonySafetyService
{
    /** Hide profiles that the current user has reported. */
    public function hideReportedProfiles(Builder $query, int $userId): Builder
    {
        return $query->whereNotIn('user_id', MatrimonyReport::query()
            ->where('reporter_id', $userId)
            ->select('reported_user_id'));
    }

    public function hasReported(int $reporterId, int $reportedUserId): bool
    {
        return MatrimonyReport::where('reporter_id', $reporterId)
            ->where('reported_user_id', $reportedUserId)
            ->exists();
    }

    public function isBlockedBy(int $blockerId, int $blockedUserId): bool
    {
        return MatrimonyBlock::where('blocker_id', $blockerId)
            ->where('blocked_user_id', $blockedUserId)
            ->exists();
    }

    public function eitherUserBlocked(int $firstUserId, int $secondUserId): bool
    {
        return MatrimonyBlock::where(function ($query) use ($firstUserId, $secondUserId) {
            $query->where('blocker_id', $firstUserId)->where('blocked_user_id', $secondUserId);
        })->orWhere(function ($query) use ($firstUserId, $secondUserId) {
            $query->where('blocker_id', $secondUserId)->where('blocked_user_id', $firstUserId);
        })->exists();
    }
}
