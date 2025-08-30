<?php

namespace App\Modules\Messaging\DataTransferObjects;

use App\Modules\Messaging\Enums\ConversationChannelEnum;
use App\Modules\Messaging\Models\Conversation;
use Illuminate\Support\Carbon;

class ConversationData extends DataTransferObject
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string $avatar,
        public readonly string  $lastMessage,
        public readonly Carbon  $lastMessageTime,
        public readonly int     $unreadCount,
        public readonly bool    $hasUnreadMessages,
        public readonly string  $provider,
        public readonly array   $contact
    )
    {
    }

    public static function fromModel(Conversation $conversation): self
    {
        return new self(
            id: $conversation->id,
            name: $conversation->contact->name,
            avatar: $conversation->contact->avatar_path,
            lastMessage: self::getLastMessagePreview($conversation),
            lastMessageTime: $conversation->lastMessage?->created_at ?? $conversation->updated_at,
            unreadCount: $conversation->unread_counts,
            hasUnreadMessages: $conversation->has_unread_messages,
            provider: $conversation->channel->value,
            contact: [
                'id' => $conversation->contact->id,
                'name' => $conversation->contact->name,
                'email' => $conversation->contact->email,
                'phone' => $conversation->contact->phone,
                'avatar_url' => $conversation->contact->avatar_path,
            ]
        );
    }

    private static function getLastMessagePreview(Conversation $conversation): string
    {
        $lastMessage = $conversation->messages()->latest()->first();

        if (!$lastMessage) {
            return 'Sem mensagens';
        }

        if ($conversation->channel === ConversationChannelEnum::Email && $lastMessage->subject) {
            return $lastMessage->subject;
        }

        if (filled($lastMessage->content)) {
            return mb_strlen($lastMessage->content) > 40
                ? mb_substr($lastMessage->content, 0, 40) . '...'
                : $lastMessage->content;
        }

        return 'Mensagem sem conteúdo';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'lastMessage' => $this->lastMessage,
            'lastMessageTime' => $this->lastMessageTime,
            'unreadCount' => $this->unreadCount,
            'hasUnreadMessages' => $this->hasUnreadMessages,
            'provider' => $this->provider,
            'contact' => $this->contact,
        ];
    }
}

