<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\BirthdayNotificationService;
use App\Services\NotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Tests\TestCase;

class BirthdayNotificationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'birthday_testing');
        config()->set('database.connections.birthday_testing', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        DB::purge('birthday_testing');
        DB::setDefaultConnection('birthday_testing');
        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email'); $table->date('dob')->nullable();
            $table->string('status')->default('active'); $table->string('password')->nullable(); $table->timestamps();
        });
        Schema::create('caste_certificates', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('file_path')->nullable(); $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('type'); $table->timestamps();
        });
    }

    public function test_it_sends_only_to_active_users_with_a_birthday_today(): void
    {
        $birthdayUser = User::forceCreate(['name' => 'Birthday User', 'email' => 'birthday@example.test', 'dob' => now()->subYears(30)->toDateString(), 'status' => 'active']);
        User::forceCreate(['name' => 'Other User', 'email' => 'other@example.test', 'dob' => now()->subYears(30)->addDay()->toDateString(), 'status' => 'active']);
        User::forceCreate(['name' => 'Inactive User', 'email' => 'inactive@example.test', 'dob' => now()->subYears(30)->toDateString(), 'status' => 'inactive']);
        $this->mock(NotificationService::class, function (MockInterface $mock) use ($birthdayUser) {
            $mock->shouldReceive('createNotification')->once()->withArgs(fn ($userId, $type) => $userId === $birthdayUser->id && $type === 'birthday_wish');
        });

        $this->assertSame(1, app(BirthdayNotificationService::class)->sendToday());
    }
}
