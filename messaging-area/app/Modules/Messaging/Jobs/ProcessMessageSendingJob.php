<?php

namespace App\Modules\Messaging\Jobs;

use App\Modules\Messaging\Concerns\ChannelInterface;
use App\Modules\Messaging\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Throwable;

class ProcessMessageSendingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly Message          $message,
        private readonly ChannelInterface $channel
    )
    {
    }

    public function handle(): void
    {
        $this->channel->processMessage($this->message);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Message sending job failed permanently', [
            'message_id' => $this->message->id,
            'channel' => $this->channel->getName(),
            'error' => $exception->getMessage(),
        ]);

        if ($this->attempts() >= $this->tries) {
            $this->message->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'failed_at' => now(),
            ]);
        }
    }
}
