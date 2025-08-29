<?php

namespace App\Modules\Messaging\Channels;

use App\Modules\Messaging\Concerns\ChannelInterface;
use App\Modules\Messaging\Enums\MessageStatusEnum;
use App\Modules\Messaging\Models\Message;
use Exception;
use Illuminate\Support\Facades\Log;

abstract class AbstractChannel implements ChannelInterface
{
    public function sendMessage(Message $message): void
    {
        $this->logEvent($message, 'queued', ['queued_at' => now()]);

    }

    protected function logEvent(Message $message, string $event, array $context = [], ?string $error = null): void
    {
        Log::info("Message {$event}", [
            'message_id' => $message->id,
            'channel' => $this->getName(),
            'context' => $context,
            'error' => $error,
        ]);
    }

    protected function updateMessageStatus(Message $message, string $status, ?string $error = null): void
    {
        match ($status) {
            'sent' => $message->update(['status' => MessageStatusEnum::Sent]),
            'delivered' => $message->update(['status' => MessageStatusEnum::Delivered]),
            'read' => $message->update(['status' => MessageStatusEnum::Read]),
            'failed' => $message->update(['status' => MessageStatusEnum::Failed, 'error_message' => $error]),
            default => $message->update(['status' => MessageStatusEnum::Failed]),
        };
    }

    /**
     * @throws Exception
     */
    protected function simulateProcessing(): void
    {
        sleep($this->getSimulationDelay());

        if (rand(1, 100) <= 5) {
            throw new \Exception("Simulação de falha no canal {$this->getName()}");
        }
    }

    /**
     * @throws Exception
     */
    public function processMessage(Message $message): void
    {
        $this->logEvent($message, 'processing');

        try {
            $this->simulateProcessing();

            $this->updateMessageStatus($message, 'sent');
            $this->logEvent($message, 'sent');

            sleep(1);
            $this->updateMessageStatus($message, 'delivered');
            $this->logEvent($message, 'delivered');

        } catch (\Exception $e) {
            $this->updateMessageStatus($message, 'failed', $e->getMessage());
            $this->logEvent($message, 'failed', [], $e->getMessage());
            throw $e;
        }
    }
}
