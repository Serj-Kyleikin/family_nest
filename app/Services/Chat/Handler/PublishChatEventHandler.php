<?php

namespace App\Services\Chat\Handler;

use App\Models\Chat\ChatMembers;
use App\Services\{
    Chat\Repositories\ChatMemberRepository,
    Family\Repositories\FamilyUserRepository,
};
use Illuminate\Support\{
    Facades\Http,
    Facades\Log,
    Str,
};

class PublishChatEventHandler
{
    private const TEXT_PREVIEW_LIMIT = 100;

    public function __construct(
        private readonly ChatMemberRepository   $chatMemberRepository,
        private readonly FamilyUserRepository   $familyUserRepository,
    )
    {
    }

    public function handleUpdated(int $chatId): void
    {
        $this->publish('chat.updated', $this->getMemberIds($chatId), ['chat_id' => $chatId]);
    }

    public function handleMessage(int $chatId, int $senderId, string $senderName, string|null $text): void
    {
        $recipientIds       = array_values(array_diff($this->getMemberIds($chatId), [$senderId]));
        $aliases            = $this->familyUserRepository->getAliasesByUserIds($senderId, $recipientIds);
        $recipientsByName   = collect($recipientIds)
            ->groupBy(fn (int $recipientId): string => strval($aliases[$recipientId] ?? $senderName));

        foreach ($recipientsByName as $name => $ids) {
            $this->publish('chat.message', $ids->values()->all(), [
                'chat_id'   => $chatId,
                'user_name' => strval($name),
                'text'      => Str::limit(strval($text), self::TEXT_PREVIEW_LIMIT),
            ]);
        }
    }

    private function getMemberIds(int $chatId): array
    {
        return $this->chatMemberRepository
            ->getWhere(['chat_id' => $chatId], 'user_id')
            ->map(fn (ChatMembers $member): int => intval($member->user_id))
            ->values()
            ->all();
    }

    private function publish(string $name, array $userIds, array $data): void
    {
        $url    = strval(config('services.websock.url'));
        $secret = strval(config('services.websock.secret'));

        $channels = array_map(
            fn (int $userId): string => ChatMembers::USER_CHANNEL_PREFIX . $userId,
            $userIds
        );

        $isPublishAvailable = filled($url) && filled($secret) && count($channels) > 0;

        if ($isPublishAvailable) {
            $body = json_encode([
                'name'      => $name,
                'channels'  => $channels,
                'data'      => json_encode($data),
            ]);

            try {
                Http::baseUrl($url)
                    ->timeout(2)
                    ->withHeaders(['X-Signature' => hash_hmac('sha256', $body, $secret)])
                    ->withBody($body, 'application/json')
                    ->post('/events');
            } catch (\Throwable $exception) {
                Log::warning('Websock publish failed: ' . $exception->getMessage());
            }
        }
    }
}
