<?php

namespace App\Services\Channels;

use App\Models\Message;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class EmailSender implements ChannelSenderInterface
{
	public function send(Message $message): array
	{
		Log::info('Email send start', ['message_id' => $message->id]);

		$delay = random_int(1, 3);
		sleep($delay);

		// 5% de chance de erro simulado
		if (random_int(1, 100) <= 5) {
			Log::error('Email send failed', ['message_id' => $message->id]);
			throw new Exception('Email simulated failure');
		}

		$sentAt = Carbon::now();
		Log::info('Email send success', ['message_id' => $message->id, 'sent_at' => $sentAt->toISOString()]);

		return [
			'ok' => true,
			'sent_at' => $sentAt,
		];
	}
}
