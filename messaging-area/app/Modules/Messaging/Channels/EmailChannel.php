<?php

namespace App\Modules\Messaging\Channels;

use App\Modules\Messaging\Models\Message;
use Exception;

class EmailChannel extends AbstractChannel
{
    public function getName(): string
    {
        return 'email';
    }


    public function getSimulationDelay(): int
    {
        return rand(2, 3);
    }
}
