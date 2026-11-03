<?php

namespace App\Services;

use App\Models\User;
use App\Models\Webinar;
use Peterujah\Agora\Agora;
use Peterujah\Agora\Builders\RtcToken;
use Peterujah\Agora\Roles;
use Peterujah\Agora\User as AgoraUser;
use RuntimeException;

class AgoraTokenService
{
    public function credentials(Webinar $webinar, User $user, bool $isHost): array
    {
        $appId = (string) config('services.agora.app_id');
        $certificate = (string) config('services.agora.app_certificate');

        if ($appId === '' || $certificate === '') {
            throw new RuntimeException('Agora is not configured. Add AGORA_APP_ID and AGORA_APP_CERTIFICATE to the server environment.');
        }

        $expiresIn = max(300, min((int) config('services.agora.token_expiration', 3600), 86400));
        $account = ($isHost ? 'host-' : 'user-').$user->getKey();

        $client = (new Agora($appId, $certificate))->setExpiration($expiresIn);
        $agoraUser = (new AgoraUser($account))
            ->setChannel($webinar->agora_channel)
            ->setRole($isHost ? Roles::RTC_PUBLISHER : Roles::RTC_SUBSCRIBER)
            ->setPrivilegeExpire($expiresIn);

        return [
            'app_id' => $appId,
            'channel' => $webinar->agora_channel,
            'uid' => $account,
            'token' => RtcToken::buildTokenWithUserAccount($client, $agoraUser),
            'expires_in' => $expiresIn,
            'role' => $isHost ? 'host' : 'audience',
        ];
    }
}
