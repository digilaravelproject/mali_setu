<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Webinar;
use App\Services\DailyMeetingService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DailyMeetingServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'daily_testing');
        config()->set('database.connections.daily_testing', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        DB::purge('daily_testing');
        DB::setDefaultConnection('daily_testing');
        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email'); $table->string('password')->nullable(); $table->timestamps();
        });
        Schema::create('caste_certificates', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('file_path')->nullable(); $table->timestamps();
        });
        Schema::create('webinars', function (Blueprint $table) {
            $table->id(); $table->dateTime('starts_at'); $table->unsignedInteger('duration_minutes');
            $table->string('meeting_provider')->default('daily'); $table->string('daily_room_name')->nullable();
            $table->text('daily_room_url')->nullable(); $table->timestamps();
        });
        config()->set('services.daily', ['api_key' => 'test-key', 'api_url' => 'https://api.daily.co/v1/', 'timeout' => 5]);
    }

    public function test_it_creates_private_room_and_returns_host_token(): void
    {
        Http::fake([
            'https://api.daily.co/v1/rooms/*' => Http::response([], 404),
            'https://api.daily.co/v1/rooms' => Http::response(['name' => 'mali-setu-webinar-1', 'url' => 'https://example.daily.co/mali-setu-webinar-1']),
            'https://api.daily.co/v1/meeting-tokens' => Http::response(['token' => 'daily-token']),
        ]);
        $user = User::forceCreate(['name' => 'Host', 'email' => 'host@example.test']);
        $webinar = Webinar::forceCreate(['starts_at' => now(), 'duration_minutes' => 60, 'meeting_provider' => 'daily']);

        $credentials = app(DailyMeetingService::class)->credentials($webinar, $user, true);

        $this->assertSame('daily', $credentials['provider']);
        $this->assertSame('daily-token', $credentials['token']);
        $this->assertSame('https://example.daily.co/mali-setu-webinar-1', $credentials['room_url']);
        $this->assertSame('mali-setu-webinar-1', $webinar->fresh()->daily_room_name);
        Http::assertSent(fn ($request) => $request->url() === 'https://api.daily.co/v1/meeting-tokens'
            && $request['properties']['is_owner'] === true);
    }
}
