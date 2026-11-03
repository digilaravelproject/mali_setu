<?php

namespace Tests\Unit;

use App\Models\Webinar;
use Carbon\Carbon;
use Tests\TestCase;

class WebinarSchedulingTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_admin_can_go_live_from_exactly_ten_minutes_before_start(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-03 10:00:00', 'Asia/Kolkata'));

        $webinar = new Webinar([
            'status' => 'published',
            'starts_at' => Carbon::parse('2026-11-03 10:10:00', 'Asia/Kolkata'),
            'duration_minutes' => 60,
        ]);

        $this->assertTrue($webinar->adminCanGoLive());
    }

    public function test_admin_cannot_go_live_more_than_ten_minutes_early(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-03 09:59:59', 'Asia/Kolkata'));

        $webinar = new Webinar([
            'status' => 'published',
            'starts_at' => Carbon::parse('2026-11-03 10:10:00', 'Asia/Kolkata'),
            'duration_minutes' => 60,
        ]);

        $this->assertFalse($webinar->adminCanGoLive());
    }

    public function test_attendee_can_only_join_during_the_scheduled_webinar(): void
    {
        $webinar = new Webinar([
            'status' => 'published',
            'starts_at' => Carbon::parse('2026-11-03 10:10:00', 'Asia/Kolkata'),
            'duration_minutes' => 60,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-03 10:09:59', 'Asia/Kolkata'));
        $this->assertFalse($webinar->attendeeCanJoin());

        Carbon::setTestNow(Carbon::parse('2026-11-03 10:10:00', 'Asia/Kolkata'));
        $this->assertTrue($webinar->attendeeCanJoin());
    }

    public function test_existing_single_category_remains_available(): void
    {
        $webinar = new Webinar(['category' => 'business']);
        $this->assertSame(['business'], $webinar->selected_categories);

        $webinar->categories = ['business', 'matrimony'];
        $this->assertSame(['business', 'matrimony'], $webinar->selected_categories);
    }
}
