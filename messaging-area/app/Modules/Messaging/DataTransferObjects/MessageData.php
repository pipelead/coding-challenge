<?php

namespace App\Modules\Messaging\DataTransferObjects;

use App\Modules\Messaging\Enums\MessageDirectionEnum;
use App\Modules\Messaging\Models\Message;
use Illuminate\Support\Carbon;

class MessageData extends DataTransferObject
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $content,
        public readonly ?string $subject,
        public readonly Carbon  $timestamp,
        public readonly bool    $isFromUser,
        public readonly string  $status,
        public readonly string  $provider,
        public readonly int     $conversationId,
    )
    {
    }

    public static function fromModel(Message $message): self
    {
        return new self(
            id: $message->id,
            content: $message->content ?? '',
            subject: $message->subject,
            timestamp: $message->created_at,
            isFromUser: $message->direction === MessageDirectionEnum::Inbound,
            status: $message->status->value,
            provider: $message->conversation->channel->value,
            conversationId: $message->conversation_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'subject' => $this->subject,
            'created_at' => $this->timestamp,
            'isFromUser' => $this->isFromUser,
            'status' => $this->status,
            'provider' => $this->provider,
            'conversation_id' => $this->conversationId,
        ];
    }
}
