<?php

namespace App\Services\Channels;

use Exception;

class SenderResolver
{
	public function resolveBySlug(string $slug): ChannelSenderInterface
	{
		return match ($slug) {
			'whatsapp' => new WhatsAppSender(),
			'messenger' => new MessengerSender(),
			'email' => new EmailSender(),
			default => throw new Exception('Unsupported channel slug: ' . $slug),
		};
	}
}
