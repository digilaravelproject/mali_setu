<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\NotificationAction;
use App\Models\User;
use App\Services\NotificationService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NotificationPushPayloadTest extends TestCase
{
    public function test_admin_payload_is_rendered_and_merged_into_push_data(): void
    {
        $notification = new Notification([
            'type' => 'business_verified',
            'title' => 'Business verified',
            'message' => 'Your business is live.',
            'action_url' => '/business/manage',
            'data' => ['business_id' => 42, 'source' => 'event'],
        ]);
        $notification->id = 91;

        $user = new User(['name' => 'Darshan', 'email' => 'darshan@example.com']);
        $action = new NotificationAction([
            'payload_template' => [
                'type' => 'business',
                'id' => '{{business_id}}',
                'recipient' => '{{user_name}}',
                'source' => 'admin',
            ],
        ]);

        $service = (new ReflectionClass(NotificationService::class))->newInstanceWithoutConstructor();
        $method = (new ReflectionClass(NotificationService::class))->getMethod('pushData');
        $payload = $method->invoke($service, $notification, $user, $action);

        $this->assertSame('business', $payload['type']);
        $this->assertSame('42', $payload['id']);
        $this->assertSame('Darshan', $payload['recipient']);
        $this->assertSame('admin', $payload['source']);
        $this->assertSame(91, $payload['notification_id']);
        $this->assertSame('/business/manage', $payload['action_url']);
    }
}
