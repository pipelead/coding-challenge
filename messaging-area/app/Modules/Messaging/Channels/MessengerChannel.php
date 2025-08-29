<?php

namespace App\Modules\Messaging\Channels;

use App\Modules\Messaging\Models\Message;
use Exception;

class MessengerChannel extends AbstractChannel
{
    public function getName(): string
    {
        return 'messenger';
    }


    public function getSimulationDelay(): int
    {
        return rand(1, 2);
    }
}
