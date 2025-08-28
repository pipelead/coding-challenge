<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
	public function run(): void
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
