<?php

namespace Tests\Unit;

use App\Models\AdminTeamMember;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminTeamPermissionTest extends TestCase
{
    #[Test]
    public function active_team_member_only_receives_assigned_permissions(): void
    {
        $member = new AdminTeamMember([
            'permissions' => ['jobs.view', 'jobs.manage'],
            'is_active' => true,
        ]);
        $user = new User(['user_type' => 'general']);
        $user->setRelation('adminTeamMember', $member);

        $this->assertTrue($user->hasAdminPermission('jobs.view'));
        $this->assertTrue($user->hasAdminPermission('jobs.manage'));
        $this->assertTrue($user->canAccessAdminModule('jobs'));
        $this->assertFalse($user->canAccessAdminModule('payments'));
    }

    #[Test]
    public function inactive_team_member_has_no_permissions(): void
    {
        $member = new AdminTeamMember([
            'permissions' => ['users.view'],
            'is_active' => false,
        ]);
        $user = new User(['user_type' => 'general']);
        $user->setRelation('adminTeamMember', $member);

        $this->assertFalse($user->hasAdminPermission('users.view'));
    }
}
