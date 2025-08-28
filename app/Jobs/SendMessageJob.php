<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\Channels\ChannelSenderInterface;
use App\Services\Channels\SenderResolver;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendMessageJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * Número de tentativas.
	 */
	public int $tries = 3;

	/**
	 * Backoff entre tentativas (segundos).
	 *
	 * @return array<int, int>
	 */
	public function backoff(): array
	{
		return [5, 10, 30];
	}

	public function __construct(protected int $messageId)
	{
	}

	public function handle(SenderResolver $resolver): void
	{
		$message = Message::query()
			->with('channel')
			->find($this->messageId);

		if (!$message) {
			Log::error('SendMessageJob: message not found', ['message_id' => $this->messageId]);
			return;
		}

		if ($message->status !== 'sending') {
			// Já processada ou em estado final
			return;
		}

		if ($message->direction !== 'out') {
			// Apenas envia mensagens de saída
			return;
		}

		$slug = (string) ($message->channel->slug ?? '');
		$sender = $resolver->resolveBySlug($slug);

		Log::info('SendMessageJob: start', [
			'message_id' => $message->id,
			'channel' => $slug,
			'attempt' => $this->attempts(),
		]);

		try {
			$result = $sender->send($message);

			$sentAt = $result['sent_at'] instanceof Carbon
				? $result['sent_at']
				: Carbon::parse((string) $result['sent_at']);

			$message->update([
				'status' => 'sent',
				'sent_at' => $sentAt,
				'error' => null,
			]);

			Log::info('SendMessageJob: success', [
				'message_id' => $message->id,
				'sent_at' => $sentAt->toISOString(),
			]);
		} catch (Exception $e) {
			$finalAttempt = $this->attempts() >= $this->tries;

			Log::error('SendMessageJob: failure', [
				'message_id' => $message->id,
				'error' => $e->getMessage(),
				'attempt' => $this->attempts(),
				'final' => $finalAttempt,
			]);

			// Apenas marca como failed no último retry
			if ($finalAttempt) {
				$message->update([
					' status' => 'failed',
					'error' => $e->getMessage(),
				]);
			} else {
				// Armazena o erro para debug, mantém status 'sending'
				$message->update([
					'error' => $e->getMessage(),
				]);
			}

			// Propaga para retry/backoff funcionar
			throw $e;
		}
	}
}
