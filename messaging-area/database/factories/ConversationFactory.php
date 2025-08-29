<?php

namespace Database\Factories;

use App\Modules\Messaging\Enums\ConversationChannelEnum;
use App\Modules\Messaging\Models\{Contact, Conversation};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        $hasUnread = $this->faker->boolean(30);

        return [
            'contact_id' => Contact::factory(),
            'channel' => $this->faker->randomElement(ConversationChannelEnum::cases()),
            'has_unread_messages' => $hasUnread,
            'unread_counts' => $hasUnread ? $this->faker->numberBetween(1, 5) : 0,
        ];
    }
}
