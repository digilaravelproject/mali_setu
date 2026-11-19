<?php

namespace Tests\Unit;

use App\Http\Requests\StoreHomepageHeroRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class HomepageHeroRequestTest extends TestCase
{
    public function test_banner_link_accepts_internal_paths_and_web_urls(): void
    {
        $rules = (new StoreHomepageHeroRequest)->rules();

        $this->assertFalse(Validator::make(['url' => '/business'], $rules)->errors()->has('url'));
        $this->assertFalse(Validator::make(['url' => 'https://example.com/offer'], $rules)->errors()->has('url'));
    }

    public function test_banner_link_rejects_unsafe_schemes(): void
    {
        $rules = (new StoreHomepageHeroRequest)->rules();

        $this->assertTrue(Validator::make(['url' => 'javascript:alert(1)'], $rules)->errors()->has('url'));
        $this->assertTrue(Validator::make(['url' => '//example.com/offer'], $rules)->errors()->has('url'));
    }
}
