<?php

namespace App\Modules\Messaging\Concerns;

use App\Modules\Messaging\Models\Message;

interface ChannelInterface
{
    public function getName(): string;
    public function getLabel(): string;
    public function getColor(): string;
    public function getSimulationDelay(): int;
    public function supportsReadReceipts(): bool;
    public function sendMessage(Message $message): void;
}
