<?php

use App\Models\{Channel, Contact, Message};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns only new messages after last_id', function () {
	Channel::firstOrCreate(['slug' => 'email'], ['name' => 'Email']);
	$contact = Contact::factory()->create();
	$channelId = Channel::where('slug', 'email')->value('id');

	$m1 = Message::create(['contact_id' => $contact->id, 'channel_id' => $channelId, 'direction' => 'in', 'body' => 'A', 'status' => 'sent']);
	$m2 = Message::create(['contact_id' => $contact->id, 'channel_id' => $channelId, 'direction' => 'in', 'body' => 'B', 'status' => 'sent']);
	$m3 = Message::create(['contact_id' => $contact->id, 'channel_id' => $channelId, 'direction' => 'in', 'body' => 'C', 'status' => 'sent']);

	$response = $this->get("/conversations/{$contact->id}/messages/updates?last_id={$m2->id}");
	$response->assertOk();
	$response->assertJsonCount(1, 'data');
	$response->assertJsonFragment(['body' => 'C']);
});
