<?php

namespace App\Services\Channels;

use App\Models\Message;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MessengerSender implements ChannelSenderInterface
{
	public function send(Message $message): array
	{
		Log::info('Messenger send start', ['message_id' => $message->id]);

		$delay = random_int(1, 3);
		sleep($delay);

		// 10% de chance de erro simulado
		if (random_int(1, 100) <= 10) {
			Log::error('Messenger send failed', ['message_id' => $message->id]);
			throw new Exception('Messenger simulated failure');
		}

		$sentAt = Carbon::now();
		Log::info('Messenger send success', ['message_id' => $message->id, 'sent_at' => $sentAt->toISOString()]);

		return [
			'ok' => true,
			'sent_at' => $sentAt,
		];
	}
}
