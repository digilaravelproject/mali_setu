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
}
