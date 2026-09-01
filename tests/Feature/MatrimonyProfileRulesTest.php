<?php

namespace Tests\Feature;

use App\Models\MatrimonyProfile;
use App\Models\Notification;
use App\Models\User;
use App\Services\MatrimonyProfileSearchService;
use App\Services\NotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user1_id');
            $table->unsignedBigInteger('user2_id');
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
            $table->string('priority')->default('medium');
            $table->string('channel')->default('in_app');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->boolean('push_sent')->default(false);
            $table->timestamp('push_sent_at')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
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

    public function test_unfiltered_search_defaults_to_opposite_gender(): void
    {
        $request = Request::create('/api/search/matrimony', 'POST', [
            'marital_status' => 'Any',
            'physical_status' => "Doesn't Matter",
            'created_at' => 'all',
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

    public function test_explicit_filter_does_not_add_default_gender(): void
    {
        $request = Request::create('/api/search/matrimony', 'POST', ['age_min' => 21]);
        $query = MatrimonyProfile::query()->where('user_id', '!=', 1);

        app(MatrimonyProfileSearchService::class)->applyDefaultOppositeGender(
            $query,
            $request,
            User::findOrFail(1)
        );

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
            ->postJson('/api/search/matrimony', []);

        $response->assertOk()
            ->assertJsonPath('data.total', 27)
            ->assertJsonPath('data.per_page', 27)
            ->assertJsonCount(27, 'data.data');
    }

    public function test_new_profile_notifies_creator_and_all_existing_matrimony_users(): void
    {
        Mail::fake();
        $newProfile = MatrimonyProfile::findOrFail(1);

        app(NotificationService::class)->notifyMatrimonyProfileCreated($newProfile);

        $this->assertDatabaseHas('notifications', [
            'user_id' => 1,
            'type' => Notification::TYPE_MATRIMONY_PENDING,
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
}
