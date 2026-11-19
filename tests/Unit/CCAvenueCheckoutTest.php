<?php

namespace Tests\Unit;

use App\Services\CCAvenue;
use Illuminate\Http\Request;
use Tests\TestCase;

class CCAvenueCheckoutTest extends TestCase
{
    public function test_callback_url_uses_the_current_checkout_host(): void
    {
        config()->set('app.url', 'http://localhost');
        $request = Request::create('https://malisetu.com/dashboard/business/subscribe', 'POST');

        $this->assertSame(
            'https://malisetu.com/api/ccavenue/callback',
            app(CCAvenue::class)->getCallbackUrl($request)
        );
    }

    public function test_payment_url_respects_sandbox_configuration(): void
    {
        config()->set('services.ccavenue.sandbox', true);
        $this->assertStringStartsWith('https://test.ccavenue.com/', (new CCAvenue)->getPaymentUrl());

        config()->set('services.ccavenue.sandbox', false);
        $this->assertStringStartsWith('https://secure.ccavenue.com/', (new CCAvenue)->getPaymentUrl());
    }
}
