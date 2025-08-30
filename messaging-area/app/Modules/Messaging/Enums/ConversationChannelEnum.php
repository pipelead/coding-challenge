<?php

namespace App\Modules\Messaging\Enums;

enum ConversationChannelEnum: string
{
    case Email = 'email';
    case Messenger = 'messenger';
    case WhatsApp = 'whatsapp';

    public function getName(): string
    {
        return match ($this) {
            self::Email => 'Email',
            self::Messenger => 'Messenger',
            self::WhatsApp => 'WhatsApp',
        };
    }

    public static function getValues(): array
    {
        return array_map(fn ($channel) => $channel->value, self::cases());
    }
}
