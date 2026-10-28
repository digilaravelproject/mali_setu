<?php

namespace Tests\Unit;

use App\Models\NotificationAction;
use PHPUnit\Framework\TestCase;

class NotificationActionTest extends TestCase
{
    public function test_it_renders_known_variables_and_removes_missing_ones(): void
    {
        $action = new NotificationAction;

        $rendered = $action->render(
            'Hello {{ user_name }}, payment {{status}} {{missing}}',
            ['user_name' => 'Darshan', 'status' => 'approved']
        );

        $this->assertSame('Hello Darshan, payment approved ', $rendered);
    }

    public function test_default_action_keys_are_unique(): void
    {
        $keys = array_column(NotificationAction::defaults(), 'key');

        $this->assertCount(count(array_unique($keys)), $keys);
    }

    public function test_it_distinguishes_system_and_custom_actions(): void
    {
        $this->assertTrue((new NotificationAction(['key' => 'new_message']))->isSystemAction());
        $this->assertFalse((new NotificationAction(['key' => 'custom_campaign']))->isSystemAction());
    }

    public function test_it_renders_nested_payload_values_and_preserves_json_types(): void
    {
        $action = new NotificationAction([
            'payload_template' => [
                'screen' => 'business/{{ business_id }}',
                'meta' => ['recipient' => '{{user_name}}', 'featured' => true, 'attempts' => 2],
            ],
        ]);

        $payload = $action->renderPayload(['business_id' => 42, 'user_name' => 'Darshan']);

        $this->assertSame('business/42', $payload['screen']);
        $this->assertSame('Darshan', $payload['meta']['recipient']);
        $this->assertTrue($payload['meta']['featured']);
        $this->assertSame(2, $payload['meta']['attempts']);
    }
}
