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
    public function test_blog_actions_have_firebase_push_enabled(): void
    {
        $actions = collect(NotificationAction::defaults())->keyBy('key');

        foreach ([
            Notification::TYPE_BLOG_PUBLISHED,
            Notification::TYPE_BLOG_COMMENT,
            Notification::TYPE_BLOG_REPLY,
        ] as $key) {
            $this->assertTrue($actions[$key]['push_enabled']);
            $this->assertSame('blog', $actions[$key]['payload_template']['type']);
        }
    }

    public function test_birthday_action_is_visible_and_push_enabled(): void
    {
        $action = collect(NotificationAction::defaults())->firstWhere('key', Notification::TYPE_BIRTHDAY_WISH);

        $this->assertNotNull($action);
        $this->assertTrue($action['push_enabled']);
        $this->assertSame('birthday', $action['payload_template']['type']);
    }

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

    public function test_event_image_is_kept_when_an_action_payload_is_merged(): void
    {
        $notification = new Notification([
            'type' => Notification::TYPE_BLOG_PUBLISHED,
            'title' => 'New blog',
            'message' => 'A new blog was published.',
            'data' => ['blog_id' => 12, 'image_url' => 'https://example.com/tree.jpg'],
        ]);
        $notification->id = 92;
        $action = new NotificationAction([
            'payload_template' => ['type' => 'blog', 'blog_id' => '{{blog_id}}'],
        ]);

        $service = (new ReflectionClass(NotificationService::class))->newInstanceWithoutConstructor();
        $payload = (new ReflectionClass(NotificationService::class))->getMethod('pushData')
            ->invoke($service, $notification, new User(['name' => 'User']), $action);

        $this->assertSame('https://example.com/tree.jpg', $payload['image_url']);
        $this->assertSame('blog', $payload['type']);
    }
}
