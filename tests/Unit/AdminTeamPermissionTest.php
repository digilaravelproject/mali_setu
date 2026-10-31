<?php

namespace Tests\Unit;

use App\Models\AdminTeamMember;
use App\Models\User;
use App\Support\AdminPermissionRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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

    #[Test]
    public function every_admin_panel_route_is_assigned_to_a_permission_or_explicitly_exempt(): void
    {
        $unmapped = [];
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (! $name || (! str_starts_with($name, 'admin.') && ! str_starts_with($name, 'heroes.'))) {
                continue;
            }
            if (Str::is(['admin.login*', 'admin.logout', 'admin.profile*', 'admin.teams.*'], $name)) {
                continue;
            }
            if (! AdminPermissionRegistry::permissionForRoute($name)) {
                $unmapped[] = $name;
            }
        }

        $this->assertSame([], $unmapped);
    }

    #[Test]
    public function action_permissions_automatically_include_their_page_permission(): void
    {
        $this->assertSame(
            ['jobs.edit', 'jobs.list'],
            AdminPermissionRegistry::withPageDependencies(['jobs.edit'])
        );
    }
}
