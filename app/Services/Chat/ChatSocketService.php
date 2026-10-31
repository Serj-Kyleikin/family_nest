<?php

namespace App\Services\Chat;

use App\Services\Chat\Util\ChatRulesUtil;
use Illuminate\Support\Facades\Auth;

class ChatSocketService
{
    public function getAppKey(): string
    {
        return strval(config('services.websock.key'));
    }

    public function authorize(string $socketId, string $channelName): string
    {
        $userId = Auth::id();
        ChatRulesUtil::isChannelAvailableForUser($channelName, $userId);

        $secret     = strval(config('services.websock.secret'));
        $signature  = hash_hmac('sha256', $socketId . ':' . $channelName, $secret);

        return $this->getAppKey() . ':' . $signature;
    }
}
