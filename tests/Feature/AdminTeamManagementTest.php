<?php

namespace Tests\Feature;

use App\Mail\AdminTeamInvitationMail;
use App\Models\AdminTeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminTeamManagementTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function super_admin_can_create_a_team_member_and_send_login_details(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['user_type' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.teams.store'), [
            'first_name' => 'Asha',
            'last_name' => 'Patil',
            'email' => 'asha.team@example.test',
            'permissions' => ['jobs.edit'],
        ]);

        $response->assertRedirect(route('admin.teams.index'));
        $member = AdminTeamMember::with('user')->where('created_by', $admin->id)->firstOrFail();

        $this->assertSame(['jobs.edit', 'jobs.list'], $member->permissions);
        $this->assertTrue(Hash::check('Asha@123', $member->user->password));
        Mail::assertSent(AdminTeamInvitationMail::class, fn ($mail) => $mail->hasTo('asha.team@example.test'));

        $this->get(route('admin.teams.edit', $member))
            ->assertOk()
            ->assertSee('Menu, Submenu &amp; Action Permissions', false)
            ->assertSee('Menu: Jobs &amp; Careers', false)
            ->assertSee('Submenu/Page: Applications')
            ->assertSee('Change application status');
        $this->get(route('admin.users.index'))->assertDontSee('asha.team@example.test');

        $this->post(route('admin.logout'));
        $this->post(route('admin.login.post'), [
            'email' => 'asha.team@example.test',
            'password' => 'Asha@123',
        ])->assertRedirect(route('admin.jobs.index'));
        $this->assertAuthenticatedAs($member->user);
    }

    #[Test]
    public function team_member_cannot_open_an_unassigned_admin_module(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $user = User::factory()->create(['user_type' => 'general']);
        AdminTeamMember::create([
            'user_id' => $user->id,
            'created_by' => $admin->id,
            'first_name' => 'Read',
            'last_name' => 'Only',
            'permissions' => ['jobs.list'],
            'is_active' => true,
        ]);

        $this->actingAs($user)->get(route('admin.payments.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.jobs.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.jobs.create'))->assertForbidden();
    }
}
