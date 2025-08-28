<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
	public function index(Request $request)
	{
		$contacts = Contact::query()->orderBy('name')->paginate(20)->withQueryString();
		return Inertia::render('Contacts/Index', [
			'contacts' => $contacts,
		]);
	}
}
