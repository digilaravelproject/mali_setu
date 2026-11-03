<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Services\FirebasePhoneAuthService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Tests\TestCase;

class PublicUtilityApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'utility_testing');
        config()->set('database.connections.utility_testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]);
        DB::purge('utility_testing');
        DB::setDefaultConnection('utility_testing');
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key_name')->unique();
            $table->text('value');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function test_send_otp_is_public_and_returns_firebase_session_info(): void
    {
        $this->mock(FirebasePhoneAuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendVerificationCode')
                ->once()->with('+919876543210', 'captcha-token')
                ->andReturn(['sessionInfo' => 'firebase-session']);
        });

        $this->postJson('/api/auth/phone/send-otp', [
            'phone_number' => '+919876543210', 'recaptcha_token' => 'captcha-token',
        ])->assertOk()->assertJsonPath('data.session_info', 'firebase-session');
    }

    public function test_verify_phone_otp_is_public_and_returns_verified_identity(): void
    {
        $this->mock(FirebasePhoneAuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('verifyCode')->once()->with('firebase-session', '123456')->andReturn([
                'phoneNumber' => '+919876543210',
                'localId' => 'firebase-user-id',
                'idToken' => 'firebase-id-token',
                'expiresIn' => '3600',
                'isNewUser' => false,
            ]);
        });

        $this->postJson('/api/auth/phone/verify-otp', [
            'session_info' => 'firebase-session', 'otp' => '123456',
        ])->assertOk()
            ->assertJsonPath('data.verified', true)
            ->assertJsonPath('data.phone_number', '+919876543210')
            ->assertJsonPath('data.firebase_uid', 'firebase-user-id');
    }

    public function test_resend_otp_returns_a_new_firebase_session(): void
    {
        $this->mock(FirebasePhoneAuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendVerificationCode')->once()
                ->with('+919876543210', 'new-captcha-token')
                ->andReturn(['sessionInfo' => 'new-firebase-session']);
        });

        $this->postJson('/api/auth/phone/resend-otp', [
            'phone_number' => '+919876543210', 'recaptcha_token' => 'new-captcha-token',
        ])->assertOk()
            ->assertJsonPath('message', 'OTP resent successfully.')
            ->assertJsonPath('data.session_info', 'new-firebase-session');
    }

    public function test_send_otp_rejects_documentation_placeholder_as_a_token(): void
    {
        $this->postJson('/api/auth/phone/send-otp', [
            'phone_number' => '+919876543210', 'recaptcha_token' => 'FIREBASE_RECAPTCHA_TOKEN',
        ])->assertUnprocessable()->assertJsonValidationErrors('recaptcha_token');
    }

    public function test_support_numbers_are_available_without_authentication(): void
    {
        SystemSetting::setValue('support_number_primary', '+919876543210');
        SystemSetting::setValue('support_number_secondary', '+919812345678');

        $this->getJson('/api/support-numbers')->assertOk()
            ->assertJsonPath('data.support_numbers.0.number', '+919876543210')
            ->assertJsonPath('data.support_numbers.1.number', '+919812345678');
    }
}
