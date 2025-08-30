<?php

namespace App\Modules\Messaging\Enums;

enum MessageDirectionEnum: string
{
    case Inbound = 'inbound';
    case Outbound = 'outbound';

    public function isInbound(): bool
    {
        return $this === self::Inbound;
    }

    public function isOutbound(): bool
    {
        return $this === self::Outbound;
    }
}
