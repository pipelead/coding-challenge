<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$direction = $this->faker->randomElement(['in', 'out']);
		return [
			'contact_id' => Contact::factory(),
			'channel_id' => Channel::query()->inRandomOrder()->value('id') ?? 1,
			'direction' => $direction,
			'body' => $this->faker->realTextBetween(10, 140),
			'status' => $direction === 'in' ? 'sent' : 'sending',
			'meta' => [],
			'sent_at' => $direction === 'in' ? now() : null,
		];
	}
}
