<?php

namespace App\Modules\Messaging\Channels;

use App\Modules\Messaging\Models\Message;
use Exception;

class WhatsappChannel extends AbstractChannel
{
    public function getName(): string
    {
        return 'whatsapp';
    }


    public function getSimulationDelay(): int
    {
        return rand(1, 3);
    }
}
