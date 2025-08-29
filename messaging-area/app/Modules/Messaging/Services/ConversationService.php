<?php

namespace App\Modules\Messaging\Services;

use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Repositories\{ConversationRepository, MessageRepository};
use App\Modules\Messaging\DataTransferObjects\{ConversationData, MessageData};
use App\Modules\Messaging\Enums\{MessageDirectionEnum, MessageStatusEnum};
use App\Modules\Messaging\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class ConversationService
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly MessageRepository      $messageRepository,
        private readonly ChannelService         $channelManager
    )
    {
    }

    public function getConversationsForChannel(string $channel): Collection
    {
        $conversations = $this->conversationRepository->getByChannelOptimized(
            channel: $channel,
            limit: 50
        );

        return $conversations->map(function (Conversation $conversation) {
            return ConversationData::fromModel($conversation);
        });
    }

    public function getConversationWithMessages(int $conversationId): array
    {
        $data = $this->conversationRepository->getWithMessages(
            conversationId:$conversationId,
            perPage: 20
        );

        return [
            'conversation' => ConversationData::fromModel($data['conversation']),
            'messages' => $data['messages']->through(fn($message) => MessageData::fromModel($message)->toArray()),
        ];
    }

    /**
     * @throws Throwable
     */
    public function createMessage(int $conversationId, array $messageData): Message
    {
        return DB::transaction(function () use ($conversationId, $messageData) {
            $message = $this->messageRepository->create(array_merge($messageData, [
                'conversation_id' => $conversationId,
                'direction' => MessageDirectionEnum::Outbound,
                'status' => MessageStatusEnum::Pending,
            ]));

            $this->channelManager->sendMessage($message);

            return $message;
        });
    }

    /**
     * @throws Throwable
     */
    public function markAsRead(int $conversationId): bool
    {
        return $this->conversationRepository->markAsRead($conversationId);
    }

    /**
     * @throws Throwable
     */
    public function getConversationData(?int $conversationId): ?array
    {
        if (!$conversationId) {
            return null;
        }

        $conversationData = $this->getConversationWithMessages($conversationId);
        $this->markAsRead($conversationId);

        return $conversationData;
    }
}
