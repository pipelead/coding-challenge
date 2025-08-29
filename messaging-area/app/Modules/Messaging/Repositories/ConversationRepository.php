<?php

namespace App\Modules\Messaging\Repositories;

use App\Modules\Messaging\Enums\MessageDirectionEnum;
use App\Modules\Messaging\Enums\MessageStatusEnum;
use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConversationRepository
{
    public function getByChannelOptimized(string $channel, int $limit = 50): Collection
    {
        return Conversation::where('channel', $channel)
            ->with([
                'contact:id,name,email,phone,avatar_path',
                'messages' => function (Builder $query) {
                    $query->latest(column: 'created_at')->limit(value: 1);
                },
            ])
            ->withCount(['unreadMessages'])
            ->orderBy(
                Message::selectRaw('MAX(created_at)')
                    ->whereColumn('conversation_id', 'conversations.id'),
                'desc'
            )
            ->orderBy('conversations.created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getWithMessages(int $conversationId, int $perPage = 20): array
    {
        /** @var Conversation $conversation */
        $conversation = Conversation::with('contact')->findOrFail(id: $conversationId);

        $messages = $conversation->messages()
            ->with('conversation:id,channel')
            ->select(columns: [
                'id', 'conversation_id', 'direction', 'content',
                'subject', 'status', 'created_at',
            ])
            ->latest()
            ->paginate(perPage: $perPage);

        return [
            'conversation' => $conversation,
            'messages' => $messages,
        ];
    }

    /**
     * @throws Throwable
     */
    public function markAsRead(int $conversationId): bool
    {
        return DB::transaction(function () use ($conversationId) {
            /** @var Conversation $conversation */
            $conversation = Conversation::find($conversationId);

            if (!$conversation || !$conversation->has_unread_messages) {
                return false;
            }

            $conversation->messages()
                ->where(column: 'direction', value: MessageDirectionEnum::Inbound)
                ->whereIn(column: 'status', values: [MessageStatusEnum::Pending, MessageStatusEnum::Sent])
                ->update(values: [
                    'status' => MessageStatusEnum::Read,
                ]);

            $conversation->update(attributes: [
                'has_unread_messages' => false,
                'unread_counts' => 0,
            ]);

            return true;
        });
    }

    /**
     * @throws Throwable
     */
    public function create(array $data): Message
    {
        return DB::transaction(function () use ($data) {
            /** @var Message $message */
            $message = Message::create($data);

            $conversation = $message->conversation;
            $conversation->touch();

            if ($message->direction === MessageDirectionEnum::Inbound) {
                $conversation->increment(column: 'unread_counts');
                $conversation->update(attributes: ['has_unread_messages' => true]);
            }

            return $message;
        });
    }

    public function update(int $id, array $data): bool
    {
        /** @var Conversation $conversation */
        $conversation = Conversation::find($id);
        if (!$conversation) {
            return false;
        }

        return $conversation->update(attributes: $data);
    }
}
