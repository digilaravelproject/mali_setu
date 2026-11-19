<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Tests\TestCase;

class BusinessRegistrationApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'business_registration_testing');
        config()->set('database.connections.business_registration_testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]);
        DB::purge('business_registration_testing');
        DB::setDefaultConnection('business_registration_testing');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('user_type')->nullable();
            $table->timestamps();
        });
        Schema::create('caste_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
        Schema::create('business_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('business_name');
            $table->string('business_type');
            $table->unsignedBigInteger('category_id');
            $table->text('description');
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('country');
            $table->string('state');
            $table->string('district');
            $table->string('village')->nullable();
            $table->string('taluka')->nullable();
            $table->text('address')->nullable();
            $table->string('city');
            $table->string('pincode');
            $table->string('website')->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_24_7')->default(false);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('verification_status');
            $table->string('subscription_status');
            $table->json('photo')->nullable();
            $table->timestamps();
        });

        Queue::fake();
        $this->mock(NotificationService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createNotification')->once()->andReturn(null);
        });
    }

    public function test_registration_accepts_24_7_without_opening_or_closing_times(): void
    {
        $categoryId = DB::table('business_categories')->insertGetId([
            'name' => 'Agriculture', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $user = User::create([
            'name' => 'Owner', 'email' => 'owner@example.test', 'user_type' => 'general',
        ]);
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/business/register', [
            'business_name' => 'Always Open Store',
            'business_type' => 'Private Ltd',
            'category_id' => $categoryId,
            'description' => 'Open throughout the day.',
            'country' => 'India',
            'state' => 'Maharashtra',
            'district' => 'Pune',
            'city' => 'Pune',
            'pincode' => '411001',
            // Matches multipart/form-data clients, which send booleans as strings.
            'is_24_7' => 'true',
        ])->assertCreated()
            ->assertJsonPath('data.business.is_24_7', true)
            ->assertJsonPath('data.business.opening_time', null)
            ->assertJsonPath('data.business.closing_time', null);

        $this->assertDatabaseHas('businesses', [
            'business_name' => 'Always Open Store',
            'is_24_7' => true,
            'opening_time' => null,
            'closing_time' => null,
        ]);
    }
}
