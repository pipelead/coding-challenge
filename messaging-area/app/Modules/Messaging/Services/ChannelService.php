<?php

namespace App\Modules\Messaging\Services;

use App\Modules\Messaging\Channels\{EmailChannel, MessengerChannel, WhatsappChannel};
use App\Modules\Messaging\Concerns\ChannelInterface;
use App\Modules\Messaging\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ChannelService
{
    private array $channels = [];

    public function __construct()
    {
        $this->registerDefaultChannels();
    }

    private function registerDefaultChannels(): void
    {
        $this->register(new WhatsAppChannel());
        $this->register(new MessengerChannel());
        $this->register(new EmailChannel());
    }

    public function register(ChannelInterface $channel): void
    {
        $this->channels[$channel->getName()] = $channel;
    }

    public function get(string $name): ChannelInterface
    {
        if (!isset($this->channels[$name])) {
            throw new InvalidArgumentException("Channel '{$name}' not found");
        }

        return $this->channels[$name];
    }

    public function all(): Collection
    {
        return collect($this->channels);
    }

    public function getChannelInfo(): array
    {
        return $this->all()->map(fn ($channel) => [
            'value' => $channel->getName(),
        ])->values()->toArray();
    }

    public function sendMessage(Message $message): void
    {
        $channel = $this->get($message->conversation->channel->value);
        Log::info('channel', ['channel' => $channel->getName()]);

        $channel->sendMessage($message);
    }
}
