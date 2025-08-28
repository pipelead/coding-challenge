<?php

namespace App\Services\Channels;

use App\Models\Message;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsAppSender implements ChannelSenderInterface
{
	public function send(Message $message): array
	{
		Log::info('WhatsApp send start', ['message_id' => $message->id]);

		$delay = random_int(1, 3);
		sleep($delay);

		// 15% de chance de erro simulado
		if (random_int(1, 100) <= 15) {
			Log::error('WhatsApp send failed', ['message_id' => $message->id]);
			throw new Exception('WhatsApp simulated failure');
		}

		$sentAt = Carbon::now();
		Log::info('WhatsApp send success', ['message_id' => $message->id, 'sent_at' => $sentAt->toISOString()]);

		return [
			'ok' => true,
			'sent_at' => $sentAt,
		];
	}
}
