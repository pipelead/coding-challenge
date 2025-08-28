<?php

use App\Models\{Channel, Contact};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a message and persists to DB', function () {
	// seed channels
	Channel::firstOrCreate(['slug' => 'whatsapp'], ['name' => 'WhatsApp']);
	$contact = Contact::factory()->create();

	$response = $this->post(route('messages.store'), [
		'contact_id' => $contact->id,
		'channel_slug' => 'whatsapp',
		'body' => 'Hello there',
	]);

	$response->assertRedirect();
	$this->assertDatabaseHas('messages', [
		'contact_id' => $contact->id,
		'status' => 'sending',
		'body' => 'Hello there',
	]);
});
