<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessageJob;
use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MessageController extends Controller
{
	public function store(Request $request)
	{
		$validated = $request->validate([
			'contact_id' => ['required', 'integer', 'exists:contacts,id'],
			'channel_slug' => ['required', 'string', 'exists:channels,slug'],
			'body' => ['required', 'string'],
		]);

		$contact = Contact::findOrFail($validated['contact_id']);
		$channel = Channel::where('slug', $validated['channel_slug'])->firstOrFail();

		$message = Message::create([
			'contact_id' => $contact->id,
			'channel_id' => $channel->id,
			'direction' => 'out',
			'body' => $validated['body'],
			'status' => 'sending',
			'meta' => [
				'client_sent_at' => Carbon::now()->toISOString(),
			],
		]);

		SendMessageJob::dispatch($message->id);

		return redirect()->route('conversations.show', ['contact' => $contact->id]);
	}

	public function updates(Contact $contact, Request $request)
	{
		$lastId = $request->integer('last_id');
		$since = $request->input('since');

		$messages = Message::query()
			->with('channel')
			->where('contact_id', $contact->id)
			->when($lastId, fn ($q) => $q->where('id', '>', $lastId))
			->when($since, fn ($q) => $q->where('created_at', '>', Carbon::parse($since)))
			->orderBy('created_at', 'asc')
			->limit(200)
			->get();

		return response()->json([
			'data' => $messages,
		]);
	}
}
