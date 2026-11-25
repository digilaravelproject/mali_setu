<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\MatrimonyProfile;
use App\Models\Notification;
use App\Models\User;
use App\Services\MatrimonyProfileSearchService;
use App\Services\NotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MatrimonyProfileRulesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'matrimony_testing');
        config()->set('database.connections.matrimony_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('matrimony_testing');
        DB::setDefaultConnection('matrimony_testing');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('user_type')->default('matrimony');
            $table->string('cast_certificate')->nullable();
            $table->string('caste_verification_status')->default('pending');
            $table->string('status')->default('active');
            $table->string('fcm_token')->nullable();
            $table->timestamps();
        });
        Schema::create('caste_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('file_path');
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
        Schema::create('matrimony_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('age')->default(25);
            $table->json('personal_details')->nullable();
            $table->string('approval_status')->default('pending');
            $table->timestamp('profile_expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('connection_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        Schema::create('matrimony_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('reported_user_id');
            $table->string('reason_key');
            $table->text('details')->nullable();
            $table->timestamps();
            $table->unique(['reporter_id', 'reported_user_id']);
        });
        Schema::create('matrimony_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blocker_id');
            $table->unsignedBigInteger('blocked_user_id');
            $table->timestamps();
            $table->unique(['blocker_id', 'blocked_user_id']);
        });
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user1_id');
            $table->unsignedBigInteger('user2_id');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->text('message_text');
            $table->string('message_type')->default('text');
            $table->string('attachment_path')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('action_url')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('priority')->default('medium');
            $table->string('channel')->default('in_app');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->boolean('push_sent')->default(false);
            $table->timestamp('push_sent_at')->nullable();
            $table->timestamp('push_attempted_at')->nullable();
            $table->text('push_error')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamps();
        });
        Schema::create('notification_actions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('title_template');
            $table->text('body_template');
            $table->string('action_url')->nullable();
            $table->json('available_variables')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('push_enabled')->default(false);
            $table->json('payload_template')->nullable();
            $table->timestamps();
        });
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('blog_type')->nullable();
            $table->json('tags')->nullable();
            $table->text('media_path')->nullable();
            $table->string('media_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        foreach ([
            1 => ['Current Male', 'male'],
            2 => ['Female One', 'female'],
            3 => ['Female Two', 'Female'],
            4 => ['Male Two', 'male'],
        ] as $id => [$name, $gender]) {
            DB::table('users')->insert([
                'id' => $id,
                'name' => $name,
                'email' => "user{$id}@example.test",
                'user_type' => 'matrimony',
                'cast_certificate' => $id === 2 ? 'caste-certificates/user-2.pdf' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('matrimony_profiles')->insert([
                'id' => $id,
                'user_id' => $id,
                'personal_details' => json_encode(['name' => $name, 'gender' => $gender]),
                'approval_status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_profile_user_id_includes_cast_certificate(): void
    {
        $this->actingAs(User::findOrFail(1), 'sanctum')
            ->getJson('/api/matrimony/profile_user_id/2')
            ->assertOk()
            ->assertJsonPath(
                'data.profile.user.cast_certificate',
                'caste-certificates/user-2.pdf'
            );
    }

    public function test_update_profile_accepts_and_returns_cast_certificate(): void
    {
        Storage::fake('public');
        MatrimonyProfile::findOrFail(1)->update([
            'personal_details' => ['name' => 'Current Male', 'gender' => 'male', 'photos' => ['old-1.jpg', 'old-2.jpg']],
        ]);
        $this->mock(NotificationService::class, function ($mock) {
            $mock->shouldReceive('createNotification')->once();
        });

        $response = $this->actingAs(User::findOrFail(1), 'sanctum')
            ->post('/api/matrimony/profile', [
                '_method' => 'PUT',
                'cast_certificate' => UploadedFile::fake()->create('caste.pdf', 100, 'application/pdf'),
                'personal_details' => ['name' => 'Updated Name', 'gender' => 'male'],
            ]);

        $response->assertOk()
            ->assertJsonPath('data.cast_certificate', fn ($path) => str_starts_with($path, 'caste-certificates/'))
            ->assertJsonPath('data.cast_certificate_url', fn ($url) => str_contains($url, '/storage/caste-certificates/'));

        $user = User::findOrFail(1);
        Storage::disk('public')->assertExists($user->cast_certificate);
        $this->assertSame('pending', $user->caste_verification_status);
        $this->assertSame(['old-1.jpg', 'old-2.jpg'], MatrimonyProfile::findOrFail(1)->personal_details['photos']);
    }

    public function test_search_defaults_to_opposite_gender_even_with_other_filters(): void
    {
        // User 1 is Male. Searching with created_at / age_min should only return Females (Users 2 and 3).
        $request = Request::create('/api/search/matrimony', 'POST', [
            'marital_status' => 'Any',
            'physical_status' => "Doesn't Matter",
            'created_at' => 'one_week',
            'age_min' => 21,
        ]);
        $query = MatrimonyProfile::query()
            ->where('approval_status', 'approved')
            ->where('user_id', '!=', 1);

        app(MatrimonyProfileSearchService::class)->applyDefaultOppositeGender(
            $query,
            $request,
            User::findOrFail(1)
        );

        $this->assertSame([2, 3], $query->orderBy('id')->pluck('id')->all());
    }

    public function test_female_user_search_defaults_to_male_profiles(): void
    {
        // User 2 is Female. Searching without explicit gender filter should return Males (User 1 and User 4).
        $request = Request::create('/api/search/matrimony', 'POST', [
            'created_at' => 'one_week',
        ]);
        $query = MatrimonyProfile::query()
            ->where('approval_status', 'approved')
            ->where('user_id', '!=', 2);

        app(MatrimonyProfileSearchService::class)->applyDefaultOppositeGender(
            $query,
            $request,
            User::findOrFail(2)
        );

        $this->assertSame([1, 4], $query->orderBy('id')->pluck('id')->all());
    }

    public function test_explicit_gender_filter_overrides_default_opposite_gender(): void
    {
        $request = Request::create('/api/search/matrimony', 'POST', ['gender' => 'male']);
        $query = MatrimonyProfile::query()->where('user_id', '!=', 1);

        app(MatrimonyProfileSearchService::class)->applyDefaultOppositeGender(
            $query,
            $request,
            User::findOrFail(1)
        );

        // When gender=male is passed, applyDefaultOppositeGender does not filter out males.
        $this->assertSame([2, 3, 4], $query->orderBy('id')->pluck('id')->all());
    }

    public function test_api_matrimony_search_returns_all_matches_when_size_is_not_supplied(): void
    {
        foreach (range(5, 29) as $id) {
            DB::table('users')->insert([
                'id' => $id,
                'name' => "Female {$id}",
                'email' => "user{$id}@example.test",
                'user_type' => 'matrimony',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('matrimony_profiles')->insert([
                'id' => $id,
                'user_id' => $id,
                'personal_details' => json_encode(['name' => "Female {$id}", 'gender' => 'female']),
                'approval_status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->actingAs(User::findOrFail(1), 'sanctum')
            ->postJson('/api/search/matrimony', ['created_at' => 'one_week']);

        $response->assertOk()
            ->assertJsonPath('data.total', 27)
            ->assertJsonPath('data.per_page', 27)
            ->assertJsonCount(27, 'data.data');
    }

    public function test_report_api_hides_reported_profile_from_listing_and_direct_view(): void
    {
        $this->actingAs(User::findOrFail(1), 'sanctum')
            ->postJson('/api/matrimony/report-user', [
                'reported_user_id' => 2,
                'reason_key' => 'fake_profile',
                'details' => 'The identity information appears inconsistent.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.report.reason_key', 'fake_profile');

        $this->assertDatabaseHas('matrimony_reports', [
            'reporter_id' => 1, 'reported_user_id' => 2, 'reason_key' => 'fake_profile',
        ]);

        $response = $this->postJson('/api/search/matrimony', ['created_at' => 'all']);
        $returnedUserIds = collect($response->assertOk()->json('data.data'))->pluck('user_id');
        $this->assertFalse($returnedUserIds->contains(2));
        $this->getJson('/api/matrimony/profile_user_id/2')->assertNotFound();
    }

    public function test_block_api_toggles_and_listing_keeps_profile_with_blocked_status(): void
    {
        $this->actingAs(User::findOrFail(1), 'sanctum')
            ->postJson('/api/matrimony/block-user', ['blocked_user_id' => 2])
            ->assertOk()
            ->assertJsonPath('data.is_blocked', true)
            ->assertJsonPath('data.status', 'blocked');

        $response = $this->postJson('/api/search/matrimony', ['created_at' => 'all']);
        $profile = collect($response->assertOk()->json('data.data'))->firstWhere('user_id', 2);
        $this->assertNotNull($profile);
        $this->assertSame('blocked', $profile['connection_status']);
        $this->assertTrue($profile['is_blocked']);

        $this->postJson('/api/matrimony/block-user', ['blocked_user_id' => 2])
            ->assertOk()
            ->assertJsonPath('data.is_blocked', false)
            ->assertJsonPath('data.status', 'not_connected');
        $this->assertDatabaseMissing('matrimony_blocks', ['blocker_id' => 1, 'blocked_user_id' => 2]);
    }

    public function test_conversations_include_is_block_for_the_other_user(): void
    {
        DB::table('chat_conversations')->insert([
            [
                'id' => 1,
                'user1_id' => 1,
                'user2_id' => 2,
                'last_message_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user1_id' => 3,
                'user2_id' => 1,
                'last_message_at' => now()->subMinute(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('matrimony_blocks')->insert([
            'blocker_id' => 1,
            'blocked_user_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs(User::findOrFail(1), 'sanctum')
            ->getJson('/api/matrimony/conversations')
            ->assertOk();

        $conversations = collect($response->json('data.conversations'))->keyBy('id');
        $this->assertTrue($conversations[1]['is_block']);
        $this->assertFalse($conversations[2]['is_block']);
    }

    public function test_blocked_users_api_only_lists_users_blocked_by_authenticated_user(): void
    {
        DB::table('matrimony_blocks')->insert([
            [
                'blocker_id' => 1,
                'blocked_user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blocker_id' => 1,
                'blocked_user_id' => 3,
                'created_at' => now()->subMinute(),
                'updated_at' => now(),
            ],
            [
                'blocker_id' => 4,
                'blocked_user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs(User::findOrFail(1), 'sanctum')
            ->getJson('/api/matrimony/blocked-users')
            ->assertOk()
            ->assertJsonPath('data.total', 2)
            ->assertJsonPath('data.blocked_users.0.user_id', 2)
            ->assertJsonPath('data.blocked_users.0.is_block', true);

        $this->assertEqualsCanonicalizing(
            [2, 3],
            collect($response->json('data.blocked_users'))->pluck('user_id')->all()
        );
    }

    public function test_web_report_and_block_actions_persist_and_toggle(): void
    {
        $this->actingAs(User::findOrFail(1))
            ->post('/dashboard/matrimony/report-user', [
                'reported_user_id' => 2,
                'reason_key' => 'spam',
                'details' => 'Repeated unsolicited contact.',
            ])
            ->assertRedirect(route('matrimony.browse'));
        $this->assertDatabaseHas('matrimony_reports', [
            'reporter_id' => 1, 'reported_user_id' => 2, 'reason_key' => 'spam',
        ]);

        $this->post('/dashboard/matrimony/block-user', ['blocked_user_id' => 2])->assertRedirect();
        $this->assertDatabaseHas('matrimony_blocks', ['blocker_id' => 1, 'blocked_user_id' => 2]);

        $this->post('/dashboard/matrimony/block-user', ['blocked_user_id' => 2])->assertRedirect();
        $this->assertDatabaseMissing('matrimony_blocks', ['blocker_id' => 1, 'blocked_user_id' => 2]);
    }

    public function test_candidate_name_combines_personal_detail_name_fields(): void
    {
        $profile = MatrimonyProfile::findOrFail(1);
        $profile->personal_details = [
            'first_name' => 'Laxmi',
            'middle_name' => 'Jaikumar',
            'last_name' => 'Charjan',
        ];

        $this->assertSame('Laxmi Jaikumar Charjan', $profile->candidate_name);
    }

    public function test_admin_search_finds_a_profile_by_partial_candidate_name(): void
    {
        MatrimonyProfile::findOrFail(2)->update([
            'personal_details' => [
                'first_name' => 'Laxmi',
                'middle_name' => 'Jaikumar',
                'last_name' => 'Charjan2',
                'gender' => 'female',
            ],
        ]);

        $matches = MatrimonyProfile::query()
            ->adminSearch('charjan2')
            ->pluck('id')
            ->all();

        $this->assertSame([2], $matches);
    }

    public function test_admin_gender_filter_is_case_insensitive(): void
    {
        $matches = MatrimonyProfile::query()
            ->adminGender('female')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $this->assertSame([2, 3], $matches);
    }

    public function test_new_profile_broadcasts_to_matrimony_users_only_after_admin_approval(): void
    {
        Mail::fake();
        $newProfile = MatrimonyProfile::findOrFail(1);

        app(NotificationService::class)->notifyMatrimonyProfileCreated($newProfile);

        $this->assertDatabaseHas('notifications', [
            'user_id' => 1,
            'type' => Notification::TYPE_MATRIMONY_PENDING,
        ]);
        $this->assertDatabaseCount('notifications', 1);

        $newProfile->update(['approval_status' => 'approved']);
        app(NotificationService::class)->notifyMatrimonyApproved($newProfile->fresh());

        $this->assertDatabaseHas('notifications', [
            'user_id' => 1,
            'type' => Notification::TYPE_MATRIMONY_APPROVED,
        ]);
        foreach ([2, 3, 4] as $existingUserId) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $existingUserId,
                'type' => Notification::TYPE_MATRIMONY_PROFILE_CREATED,
                'related_id' => 1,
            ]);
        }
        $this->assertSame(
            3,
            Notification::where('type', Notification::TYPE_MATRIMONY_PROFILE_CREATED)->count()
        );
    }

    public function test_published_blog_creates_push_enabled_notifications_for_other_active_users(): void
    {
        $blog = Blog::create([
            'user_id' => 1,
            'title' => 'Community Update',
            'description' => 'News for members',
            'is_active' => true,
        ]);

        app(NotificationService::class)->notifyBlogPublished($blog);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => 1,
            'type' => Notification::TYPE_BLOG_PUBLISHED,
        ]);
        foreach ([2, 3, 4] as $recipientId) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $recipientId,
                'type' => Notification::TYPE_BLOG_PUBLISHED,
            ]);
        }
        $this->assertDatabaseHas('notification_actions', [
            'key' => Notification::TYPE_BLOG_PUBLISHED,
            'push_enabled' => true,
        ]);
    }
}
