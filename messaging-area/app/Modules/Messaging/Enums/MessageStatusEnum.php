<?php

namespace App\Modules\Messaging\Enums;

enum MessageStatusEnum: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Read = 'read';
    case Failed = 'failed';

    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    public function isSent(): bool
    {
        return $this === self::Sent;
    }

    public function isDelivered(): bool
    {
        return $this === self::Delivered;
    }

    public function isRead(): bool
    {
        return $this === self::Read;
    }

    public function isFailed(): bool
    {
        return $this === self::Failed;
    }
}
