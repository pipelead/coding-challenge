<?php

namespace App\Services\Channels;

use App\Models\Message;

interface ChannelSenderInterface
{
	/**
	 * Envia a mensagem no canal específico.
	 * Deve retornar algo como ['ok' => true, 'sent_at' => now()].
	 * Lança exceção para simular erro de envio.
	 *
	 * @return array{ok: bool, sent_at: \Illuminate\Support\Carbon|\DateTimeInterface}
	 */
	public function send(Message $message): array;
}
