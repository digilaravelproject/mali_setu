<?php

namespace Tests\Unit;

use App\Services\FirebaseMessagingService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FirebaseMessagingPayloadTest extends TestCase
{
    public function test_all_push_data_is_normalized_for_flutter_navigation(): void
    {
        $reflection = new ReflectionClass(FirebaseMessagingService::class);
        $service = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('canonicalData');

        $data = $method->invoke($service, ['lead_id' => 125, 'type' => 'lead']);

        $this->assertSame('lead', $data['type']);
        $this->assertSame('details', $data['action']);
        $this->assertSame('125', $data['id']);
        $this->assertSame('FLUTTER_NOTIFICATION_CLICK', $data['click_action']);
    }

    public function test_image_is_added_to_native_android_apns_and_flutter_payloads(): void
    {
        $image = 'https://images.example.com/profile.jpg';
        $reflection = new ReflectionClass(FirebaseMessagingService::class);
        $service = $reflection->newInstanceWithoutConstructor();
        $canonical = $reflection->getMethod('canonicalData')->invoke($service, [
            'type' => 'connection_request',
            'profile_id' => 350,
            'image_url' => $image,
        ]);
        $message = $reflection->getMethod('messagePayload')->invoke(
            $service,
            'device-token',
            'New Connection Request',
            'Pooja has sent you a connection request.',
            $canonical,
        );

        $this->assertSame($image, $message['notification']['image']);
        $this->assertSame('HIGH', $message['android']['priority']);
        $this->assertSame('fcm_push_channel', $message['android']['notification']['channel_id']);
        $this->assertSame($image, $message['android']['notification']['image']);
        $this->assertSame(1, $message['apns']['payload']['aps']['mutable-content']);
        $this->assertSame($image, $message['apns']['fcm_options']['image']);
        $this->assertSame($image, $message['data']['image']);
        $this->assertSame($image, $message['data']['image_url']);
        $this->assertSame('350', $message['data']['profile_id']);
    }

    public function test_full_fcm_style_custom_payload_can_supply_the_image_safely(): void
    {
        $image = 'https://images.example.com/background.png';
        $reflection = new ReflectionClass(FirebaseMessagingService::class);
        $service = $reflection->newInstanceWithoutConstructor();
        $canonical = $reflection->getMethod('canonicalData')->invoke($service, [
            'message' => [
                'token' => 'must-not-be-forwarded',
                'notification' => ['image' => $image],
                'data' => ['type' => 'connection_request', 'profile_id' => '350'],
            ],
        ]);

        $this->assertSame($image, $canonical['image']);
        $this->assertSame($image, $canonical['image_url']);
        $this->assertSame('connection_request', $canonical['type']);
        $this->assertSame('350', $canonical['id']);
        $this->assertArrayNotHasKey('message', $canonical);
        $this->assertArrayNotHasKey('token', $canonical);
    }
}
