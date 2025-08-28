<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'name' => $this->faker->name(),
			'external_id' => $this->faker->optional()->uuid(),
			'meta' => [
				'avatar' => $this->faker->imageUrl(64, 64, 'avatar', true),
			],
		];
	}
}
