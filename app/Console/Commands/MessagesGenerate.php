<?php

namespace App\Console\Commands;

use App\Jobs\SendMessageJob;
use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use Database\Factories\MessageFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessagesGenerate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'messages:generate {contacts=10} {per=20} {--dispatch}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Gera contatos e mensagens fake para simulação';

	/**
	 * Execute the console command.
	 */
	public function handle(): int
	{
		$contactsNum = (int) $this->argument('contacts');
		$per = (int) $this->argument('per');
		$shouldDispatch = (bool) $this->option('dispatch');

		$this->seedChannels();

		$this->info("Gerando {$contactsNum} contatos com {$per} mensagens cada...");

		Contact::factory($contactsNum)->create()->each(function (Contact $contact) use ($per, $shouldDispatch) {
			for ($i = 0; $i < $per; $i++) {
				$direction = fake()->randomElement(['in', 'out']);
				$channelId = Channel::inRandomOrder()->value('id');
				$message = Message::factory()->make([
					'contact_id' => $contact->id,
					'channel_id' => $channelId,
					'direction' => $direction,
					'status' => $direction === 'in' ? 'sent' : 'sending',
					'sent_at' => $direction === 'in' ? now() : null,
				]);
				$message->save();

				if ($direction === 'out' && $shouldDispatch) {
					SendMessageJob::dispatch($message->id);
				}

				usleep(random_int(50_000, 150_000));
			}
		});

		$this->info('Concluído.');
		return self::SUCCESS;
	}

	private function seedChannels(): void
	{
		$map = [
			'whatsapp' => 'WhatsApp',
			'messenger' => 'Messenger',
			'email' => 'Email',
		];
		foreach ($map as $slug => $name) {
			Channel::firstOrCreate(['slug' => $slug], ['name' => $name, 'config' => []]);
		}
	}
}
