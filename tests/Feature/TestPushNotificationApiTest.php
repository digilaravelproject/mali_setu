<?php

namespace Tests\Feature;

use App\Exceptions\FirebaseMessagingException;
use App\Services\FirebaseMessagingService;
use Mockery\MockInterface;
use Tests\TestCase;

class TestPushNotificationApiTest extends TestCase
{
    public function test_test_push_endpoint_does_not_require_authentication_or_roles(): void
    {
        $this->mock(FirebaseMessagingService::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->once()
                ->with('test-device-token', 'Test title', 'Test message', ['screen' => 'home', 'is_test' => '1'], false)
                ->andReturn(['name' => 'projects/test/messages/123']);
        });

        $response = $this->postJson('/api/notifications/test-push', [
            'fcm_token' => 'test-device-token',
            'title' => 'Test title',
            'message' => 'Test message',
            'data' => ['screen' => 'home'],
        ]);

        $response->assertOk()->assertExactJson([
            'success' => true,
            'message' => 'Test push notification sent successfully.',
            'data' => ['firebase_message_name' => 'projects/test/messages/123'],
        ]);
    }

    public function test_test_push_endpoint_returns_the_firebase_error(): void
    {
        $this->mock(FirebaseMessagingService::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')->once()->andThrow(new \RuntimeException('Requested entity was not found.'));
        });

        $response = $this->postJson('/api/notifications/test-push', [
            'fcm_token' => 'invalid-device-token',
        ]);

        $response->assertStatus(502)->assertExactJson([
            'success' => false,
            'message' => 'Test push notification could not be sent.',
            'error' => 'Requested entity was not found.',
        ]);
    }

    public function test_sender_id_mismatch_returns_actionable_resolution(): void
    {
        $this->mock(FirebaseMessagingService::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')->once()->andThrow(new FirebaseMessagingException(
                'SenderId mismatch',
                'SENDER_ID_MISMATCH',
                'malisetu-live',
                403,
                ['error' => ['message' => 'SenderId mismatch']],
            ));
        });

        $response = $this->postJson('/api/notifications/test-push', [
            'fcm_token' => 'token-from-another-project',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error_code', 'SENDER_ID_MISMATCH')
            ->assertJsonPath('configured_project_id', 'malisetu-live')
            ->assertJsonCount(4, 'resolution');
    }
}
