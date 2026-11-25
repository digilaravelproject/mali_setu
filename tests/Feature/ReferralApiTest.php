<?php

namespace Tests\Feature;

use App\Mail\ReferralLinkMail;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReferralApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'referral_testing');
        config()->set('database.connections.referral_testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]);
        DB::purge('referral_testing');
        DB::setDefaultConnection('referral_testing');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('user_type')->default('general');
            $table->string('caste_verification_status')->default('approved');
            $table->string('status')->default('active');
            $table->string('reffral_code')->nullable()->unique();
            $table->unsignedBigInteger('referred_by_user_id')->nullable();
            $table->string('photo')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('occupation')->nullable();
            $table->string('company_name')->nullable();
            $table->string('dept_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('designation')->nullable();
            $table->text('address')->nullable();
            $table->string('nearby_location')->nullable();
            $table->string('pincode')->nullable();
            $table->string('road_number')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('taluka')->nullable();
            $table->string('city')->nullable();
            $table->string('village')->nullable();
            $table->string('sector')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('respected_person_name')->nullable();
            $table->string('respected_person_mobile_number')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('caste_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
        Schema::create('volunteer_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('skills')->nullable();
            $table->text('experience')->nullable();
            $table->string('availability')->nullable();
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->text('interests')->nullable();
            $table->string('status')->default('inactive');
            $table->timestamps();
        });
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('talukas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('localities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taluka_id');
            $table->string('name');
            $table->string('type');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('volunteer_referral_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedInteger('commission_points')->default(0);
            $table->string('commission_mode')->default('default');
            $table->json('role_commission_points')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('taluka_id')->nullable();
            $table->unsignedBigInteger('locality_id')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referrer_user_id');
            $table->unsignedBigInteger('referred_user_id')->unique();
            $table->string('referral_code_used');
            $table->unsignedInteger('commission_points')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('tokenable_type');
            $table->unsignedBigInteger('tokenable_id');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('homepage_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image_path')->nullable();
            $table->string('web_image_path')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('draft');
            $table->timestamp('starts_at')->nullable();
            $table->timestamps();
        });
        Schema::create('webinar_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webinar_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_volunteer_registration_is_inactive_until_admin_activation(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Pending Volunteer',
            'email' => 'pending@example.com',
            'phone' => '9876543210',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'user_type' => 'volunteer',
            'term_condition' => true,
        ]);
        $response->assertCreated()
            ->assertJsonPath('data.user.status', 'inactive')
            ->assertJsonPath('data.token', null);

        $this->assertDatabaseHas('volunteer_profiles', ['status' => 'inactive']);
        $this->assertDatabaseHas('volunteer_referral_profiles', ['is_active' => false]);
    }

    public function test_authenticated_member_can_convert_to_volunteer(): void
    {
        $userId = DB::table('users')->insertGetId($this->userRow('General Member', 'member@example.com', '9876500000', 'general', 'MSUGENERAL'));
        Sanctum::actingAs(User::findOrFail($userId));

        $this->postJson('/api/volunteer/convert')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.user_type', 'volunteer')
            ->assertJsonPath('data.volunteer_profile.status', 'pending');

        $this->assertDatabaseHas('users', ['id' => $userId, 'user_type' => 'volunteer']);
        $this->assertDatabaseHas('volunteer_profiles', ['user_id' => $userId, 'status' => 'pending']);
        $this->assertDatabaseHas('volunteer_referral_profiles', ['user_id' => $userId, 'is_active' => false]);

        $this->postJson('/api/volunteer/convert')->assertOk();
        $this->assertSame(1, DB::table('volunteer_profiles')->where('user_id', $userId)->count());
    }

    public function test_registration_builds_referral_code_from_first_name_taluka_and_birth_date(): void
    {
        Mail::fake();

        $this->postJson('/api/auth/register', [
            'name' => 'Sunny Patil',
            'first_name' => 'Sunny',
            'taluka' => 'Mumbai',
            'dob' => '05/07/1987',
            'email' => 'sunny@example.com',
            'phone' => '9876543211',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'user_type' => 'general',
            'term_condition' => true,
        ])->assertCreated()->assertJsonPath('data.referral_code', 'sunnymumbai0507');

        $this->assertDatabaseHas('users', ['email' => 'sunny@example.com', 'reffral_code' => 'sunnymumbai0507']);
    }

    public function test_referral_endpoints_return_points_and_enforce_code_ownership(): void
    {
        $ownerId = DB::table('users')->insertGetId($this->userRow('Owner Volunteer', 'owner@example.com', '1111111111', 'volunteer', 'MSUOWNER'));
        $childId = DB::table('users')->insertGetId($this->userRow('Referred User', 'child@example.com', '2222222222', 'general', 'MSUCHILD'));
        $grandchildId = DB::table('users')->insertGetId($this->userRow('Grandchild User', 'grandchild@example.com', '2222222223', 'general', 'MSUGRAND'));
        $greatGrandchildId = DB::table('users')->insertGetId($this->userRow('Great Grandchild User', 'great-grandchild@example.com', '2222222224', 'business', 'MSUGREAT'));
        $otherId = DB::table('users')->insertGetId($this->userRow('Other User', 'other@example.com', '3333333333', 'general', 'MSUOTHER'));
        DB::table('volunteer_profiles')->insert(['user_id' => $ownerId, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('volunteer_referral_profiles')->insert(['user_id' => $ownerId, 'commission_points' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('referrals')->insert(['referrer_user_id' => $ownerId, 'referred_user_id' => $childId, 'referral_code_used' => 'MSUOWNER', 'commission_points' => 4, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('referrals')->insert(['referrer_user_id' => $childId, 'referred_user_id' => $grandchildId, 'referral_code_used' => 'MSUCHILD', 'commission_points' => 2, 'status' => 'active', 'created_at' => now()->addSecond(), 'updated_at' => now()]);
        DB::table('referrals')->insert(['referrer_user_id' => $grandchildId, 'referred_user_id' => $greatGrandchildId, 'referral_code_used' => 'MSUGRAND', 'commission_points' => 2, 'status' => 'active', 'created_at' => now()->addSeconds(2), 'updated_at' => now()]);

        $this->getJson('/api/referrals/MSUOWNER/volunteer')->assertOk()
            ->assertJsonPath('data.referral_code', 'MSUOWNER')
            ->assertJsonPath('data.total_referrals', 1);

        Sanctum::actingAs(User::findOrFail($ownerId));
        $this->getJson('/api/referrals/MSUOWNER/earnings')->assertOk()
            ->assertJsonPath('data.total_earned_points', 4)
            ->assertJsonPath('data.active_referrals', 1);
        $this->getJson('/api/referrals/MSUOWNER/referred-users')->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('root.referral_code', 'MSUOWNER')
            ->assertJsonPath('data.0.user.name', 'Referred User')
            ->assertJsonPath('data.0.user.email', 'child@example.com')
            ->assertJsonPath('data.0.user.reffral_code', 'MSUCHILD')
            ->assertJsonPath('data.0.referrer_user_id', $ownerId)
            ->assertJsonPath('data.0.referred_user_id', $childId)
            ->assertJsonPath('data.0.referral_code_used', 'MSUOWNER')
            ->assertJsonPath('data.0.commission_points', 4)
            ->assertJsonPath('data.0.status', 'active')
            ->assertJsonPath('data.0.level', 1)
            ->assertJsonPath('data.1.user.name', 'Grandchild User')
            ->assertJsonPath('data.1.level', 2)
            ->assertJsonPath('data.2.user.name', 'Great Grandchild User')
            ->assertJsonPath('data.2.level', 3)
            ->assertJsonPath('data.2.chain.0.referral_code', 'MSUOWNER')
            ->assertJsonPath('data.2.chain.3.referral_code', 'MSUGREAT')
            ->assertJsonPath('meta.max_depth', 3);

        Sanctum::actingAs(User::findOrFail($childId));
        $this->getJson('/api/referrals/MSUCHILD/referred-users')->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('root.referral_code', 'MSUCHILD')
            ->assertJsonPath('data.0.user.name', 'Grandchild User')
            ->assertJsonPath('data.0.level', 1)
            ->assertJsonPath('data.1.user.name', 'Great Grandchild User')
            ->assertJsonPath('data.1.level', 2);

        Sanctum::actingAs(User::findOrFail($otherId));
        $this->getJson('/api/referrals/MSUOWNER/history')->assertForbidden();
    }

    public function test_registration_referral_code_records_admin_assigned_points(): void
    {
        Mail::fake();
        $ownerId = DB::table('users')->insertGetId($this->userRow('Sponsor', 'sponsor@example.com', '4444444444', 'volunteer', 'MSUSPONSOR'));
        DB::table('volunteer_profiles')->insert(['user_id' => $ownerId, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('volunteer_referral_profiles')->insert(['user_id' => $ownerId, 'commission_points' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'New Member', 'email' => 'member@example.com', 'phone' => '5555555555',
            'password' => 'Password@123', 'password_confirmation' => 'Password@123',
            'user_type' => 'general', 'referral_code' => 'MSUSPONSOR', 'term_condition' => true,
        ])->assertCreated()->assertJsonPath('data.token_type', 'Bearer');

        $newUserId = $response->json('data.user.id');
        $this->assertDatabaseHas('users', ['id' => $newUserId, 'referred_by_user_id' => $ownerId]);
        $this->assertDatabaseHas('referrals', [
            'referrer_user_id' => $ownerId, 'referred_user_id' => $newUserId,
            'referral_code_used' => 'MSUSPONSOR', 'commission_points' => 7,
        ]);
    }

    public function test_registration_referral_uses_points_for_the_referred_users_role(): void
    {
        Mail::fake();
        $ownerId = DB::table('users')->insertGetId($this->userRow('Role Sponsor', 'role-sponsor@example.com', '4444444445', 'volunteer', 'MSUROLES'));
        DB::table('volunteer_profiles')->insert(['user_id' => $ownerId, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('volunteer_referral_profiles')->insert([
            'user_id' => $ownerId,
            'commission_points' => 5,
            'commission_mode' => 'role_wise',
            'role_commission_points' => json_encode(['general' => 10, 'matrimony' => 20, 'business' => 30, 'volunteer' => 40]),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Business Member', 'email' => 'business-member@example.com', 'phone' => '5555555556',
            'password' => 'Password@123', 'password_confirmation' => 'Password@123',
            'user_type' => 'business', 'referral_code' => 'MSUROLES', 'term_condition' => true,
        ])->assertCreated();

        $this->assertDatabaseHas('referrals', [
            'referrer_user_id' => $ownerId,
            'referred_user_id' => $response->json('data.user.id'),
            'commission_points' => 30,
        ]);
    }

    public function test_banner_returns_only_top_three_active_volunteers(): void
    {
        $leaderId = DB::table('users')->insertGetId($this->userRow('Referral Leader', 'leader@example.com', '6000000000', 'volunteer', 'MSULEADER'));
        DB::table('volunteer_profiles')->insert(['user_id' => $leaderId, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);

        foreach (range(1, 3) as $number) {
            $id = DB::table('users')->insertGetId($this->userRow("Volunteer {$number}", "volunteer{$number}@example.com", "700000000{$number}", 'volunteer', "MSUVOL{$number}"));
            DB::table('volunteer_profiles')->insert(['user_id' => $id, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        }
        foreach (range(1, 4) as $number) {
            $childId = DB::table('users')->insertGetId($this->userRow("Member {$number}", "leader-member{$number}@example.com", "800000000{$number}", 'general', "MSUMEM{$number}"));
            DB::table('referrals')->insert(['referrer_user_id' => $leaderId, 'referred_user_id' => $childId, 'referral_code_used' => 'MSULEADER', 'commission_points' => 2, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        }

        $this->getJson('/api/banner')->assertOk()
            ->assertJsonCount(3, 'leaderboard')
            ->assertJsonPath('leaderboard.0.name', 'Referral Leader')
            ->assertJsonPath('leaderboard.0.total_referrals', 4)
            ->assertJsonPath('leaderboard.0.rank', 1);
    }

    public function test_web_referral_link_prefills_and_locks_the_registration_field(): void
    {
        DB::table('users')->insert($this->userRow('Inviter', 'inviter@example.com', '9000000000', 'volunteer', 'MSUINVITE'));

        $this->get('/register?referral_code=MSUINVITE')->assertOk()
            ->assertSee('value="MSUINVITE"', false)
            ->assertSee('readonly', false)
            ->assertSee('Referral code applied from your invitation link.');
    }

    public function test_owner_can_send_referral_link_by_email(): void
    {
        Mail::fake();
        $ownerId = DB::table('users')->insertGetId($this->userRow('Email Inviter', 'email-inviter@example.com', '9111111111', 'volunteer', 'MSUEMAIL'));
        DB::table('volunteer_profiles')->insert(['user_id' => $ownerId, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        Sanctum::actingAs(User::findOrFail($ownerId));

        $this->postJson('/api/referrals/MSUEMAIL/send-email', [
            'email' => 'friend@example.com',
            'recipient_name' => 'Ravi Patil',
            'message' => 'Please join using my invitation.',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'friend@example.com')
            ->assertJsonPath('data.referral_code', 'MSUEMAIL');

        Mail::assertSent(ReferralLinkMail::class, function (ReferralLinkMail $mail) {
            return $mail->hasTo('friend@example.com')
                && $mail->recipientName === 'Ravi Patil'
                && str_contains($mail->referralLink, 'referral_code=MSUEMAIL');
        });
    }

    private function userRow(string $name, string $email, string $phone, string $type, string $code): array
    {
        return [
            'name' => $name, 'email' => $email, 'phone' => $phone,
            'password' => bcrypt('password'), 'user_type' => $type,
            'caste_verification_status' => 'approved', 'status' => 'active',
            'reffral_code' => $code, 'created_at' => now(), 'updated_at' => now(),
        ];
    }
}
