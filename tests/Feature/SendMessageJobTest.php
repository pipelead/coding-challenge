<?php

use App\Jobs\SendMessageJob;
use App\Models\{Channel, Contact, Message};
use App\Services\Channels\{ChannelSenderInterface, SenderResolver};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('marks message as sent on successful job', function () {
	Channel::firstOrCreate(['slug' => 'whatsapp'], ['name' => 'WhatsApp']);
	$contact = Contact::factory()->create();
	$message = Message::create([
		'contact_id' => $contact->id,
		'channel_id' => Channel::where('slug', 'whatsapp')->value('id'),
		'direction' => 'out',
		'body' => 'Test',
		'status' => 'sending',
	]);

	// Mock resolver -> sender
	$sender = new class implements ChannelSenderInterface {
		public function send(Message $message): array
		{
			return ['ok' => true, 'sent_at' => Carbon::now()];
		}
	};

	$this->instance(SenderResolver::class, new class($sender) extends SenderResolver {
		public function __construct(private ChannelSenderInterface $mock) {}
		public function resolveBySlug(string $slug): ChannelSenderInterface { return $this->mock; }
	});

	dispatch_sync(new SendMessageJob($message->id));

	$message->refresh();
	expect($message->status)->toBe('sent');
	expect($message->sent_at)->not()->toBeNull();
});
