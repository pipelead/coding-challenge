<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ConversationController extends Controller
{
	public function index(Request $request)
	{
		$cacheKey = 'contacts.index.page.' . ($request->integer('page') ?: 1);
		$contacts = Cache::remember($cacheKey, 10, function () {
			return Contact::query()
				->with(['messages' => fn ($q) => $q->select('id', 'contact_id', 'body', 'created_at')->latest()->limit(1)])
				->withCount(['messages as unread_count' => function ($q) {
					$q->where('direction', 'in')->where('status', 'sent');
				}])
				->orderBy('name')
				->paginate(20)
				->withQueryString();
		});

		return Inertia::render('Conversations/Index', [
			'contacts' => $contacts,
		]);
	}

	public function show(Contact $contact, Request $request)
	{
		$cacheKey = 'contacts.index.page.' . ($request->integer('page') ?: 1);
		$contacts = Cache::remember($cacheKey, 10, function () {
			return Contact::query()
				->with(['messages' => fn ($q) => $q->select('id', 'contact_id', 'body', 'created_at')->latest()->limit(1)])
				->withCount(['messages as unread_count' => function ($q) {
					$q->where('direction', 'in')->where('status', 'sent');
				}])
				->orderBy('name')
				->paginate(20)
				->withQueryString();
		});

		$messages = Message::query()
			->with('channel')
			->where('contact_id', $contact->id)
			->orderByDesc('created_at')
			->paginate(50)
			->withQueryString();

		return Inertia::render('Conversations/Show', [
			'contact' => $contact,
			'messages' => $messages,
			'contacts' => $contacts,
		]);
	}
}
