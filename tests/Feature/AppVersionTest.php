<?php

namespace Tests\Feature;

use App\Models\AppVersion;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AppVersionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'app_version_testing');
        config()->set('database.connections.app_version_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('app_version_testing');
        DB::setDefaultConnection('app_version_testing');

        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->unique();
            $table->string('version');
            $table->unsignedBigInteger('build_code');
            $table->string('min_version');
            $table->unsignedBigInteger('min_build');
            $table->text('store_url');
            $table->text('update_notes')->nullable();
            $table->timestamps();
        });

        AppVersion::create([
            'platform' => 'android', 'version' => '1.0.0', 'build_code' => 1,
            'min_version' => '1.0.0', 'min_build' => 1,
            'store_url' => 'https://play.google.com/store/apps/details?id=com.malisetu',
            'update_notes' => 'Android notes',
        ]);
        AppVersion::create([
            'platform' => 'apple', 'version' => '1.0.0', 'build_code' => 1,
            'min_version' => '1.0.0', 'min_build' => 1,
            'store_url' => 'https://apps.apple.com/app/mali-setu/id123456789',
            'update_notes' => 'iOS notes',
        ]);
    }

    public function test_app_version_api_is_public_and_returns_both_platforms(): void
    {
        $this->getJson('/api/app-version')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.android.version', '1.0.0')
            ->assertJsonPath('data.android.build_code', 1)
            ->assertJsonPath('data.ios.update_notes', 'iOS notes')
            ->assertJsonPath('data.app_update_url', url('/app_update'));
    }

    public function test_app_update_redirects_android_devices_to_google_play(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (Linux; Android 15; Pixel 9)')
            ->get('/app_update')
            ->assertRedirect('https://play.google.com/store/apps/details?id=com.malisetu');
    }

    public function test_app_update_redirects_apple_devices_to_app_store(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X)')
            ->get('/app_update')
            ->assertRedirect('https://apps.apple.com/app/mali-setu/id123456789');
    }

    public function test_admin_can_update_every_version_field(): void
    {
        $this->actingAs($this->admin());

        $android = AppVersion::where('platform', 'android')->firstOrFail();
        $response = $this->put(route('admin.app-versions.update', $android), [
            'version' => '2.1.0',
            'build_code' => 21,
            'min_version' => '2.0.0',
            'min_build' => 20,
            'store_url' => 'https://play.google.com/store/apps/details?id=com.malisetu.new',
            'update_notes' => 'Performance improvements.',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('admin.app-versions.index'));
        $this->assertDatabaseHas('app_versions', [
            'platform' => 'android',
            'version' => '2.1.0',
            'build_code' => 21,
            'min_version' => '2.0.0',
            'min_build' => 20,
            'update_notes' => 'Performance improvements.',
        ]);
    }

    public function test_admin_page_displays_both_platform_cards_and_edit_controls(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.app-versions.index'))
            ->assertOk()
            ->assertSee('Android Application')
            ->assertSee('iOS Application')
            ->assertSee('Edit Android Application')
            ->assertSee('Edit iOS Application');
    }

    private function admin(): User
    {
        $admin = new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_type' => 'admin']);
        $admin->id = 1;

        return $admin;
    }
}
