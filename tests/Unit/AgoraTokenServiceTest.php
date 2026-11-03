<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Webinar;
use App\Services\AgoraTokenService;
use Illuminate\Support\Facades\Config;
use Peterujah\Agora\Util;
use Tests\TestCase;

class AgoraTokenServiceTest extends TestCase
{
    public function test_it_generates_channel_and_user_bound_host_and_audience_tokens(): void
    {
        Config::set('services.agora.app_id', str_repeat('a', 32));
        Config::set('services.agora.app_certificate', str_repeat('b', 32));
        Config::set('services.agora.token_expiration', 3600);

        $webinar = new Webinar(['agora_channel' => 'webinar-test-channel']);
        $user = (new User)->forceFill(['id' => 42]);
        $tokens = new AgoraTokenService;

        $host = $tokens->credentials($webinar, $user, true);
        $audience = $tokens->credentials($webinar, $user, false);

        $this->assertSame('host-42', $host['uid']);
        $this->assertSame('user-42', $audience['uid']);
        $this->assertSame('007', substr($host['token'], 0, 3));
        $this->assertSame('007', substr($audience['token'], 0, 3));

        $hostPayload = $this->rtcPayload($host['token']);
        $audiencePayload = $this->rtcPayload($audience['token']);
        $this->assertSame([1, 2, 3, 4], array_keys($hostPayload['privileges']));
        $this->assertSame([1], array_keys($audiencePayload['privileges']));
        $this->assertSame('webinar-test-channel', $hostPayload['channel']);
        $this->assertSame('host-42', $hostPayload['uid']);
    }

    public function test_it_refuses_to_generate_tokens_without_server_credentials(): void
    {
        Config::set('services.agora.app_id', null);
        Config::set('services.agora.app_certificate', null);

        $this->expectException(\RuntimeException::class);
        (new AgoraTokenService)->credentials(
            new Webinar(['agora_channel' => 'webinar-test-channel']),
            (new User)->forceFill(['id' => 42]),
            false,
        );
    }

    private function rtcPayload(string $token): array
    {
        $data = zlib_decode(base64_decode(substr($token, 3)));
        Util::unpackString($data); // signature
        Util::unpackString($data); // app ID
        Util::unpackUint32($data); // issued at
        Util::unpackUint32($data); // token expiry
        Util::unpackUint32($data); // salt
        $this->assertSame(1, Util::unpackUint16($data));
        $this->assertSame(1, Util::unpackUint16($data));

        return [
            'privileges' => Util::unpackMapUint32($data),
            'channel' => Util::unpackString($data),
            'uid' => Util::unpackString($data),
        ];
    }
}
