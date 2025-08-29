<?php

namespace App\Modules\Messaging\Commands;

use App\Modules\Messaging\Enums\ConversationChannelEnum;
use App\Modules\Messaging\Enums\MessageDirectionEnum;
use App\Modules\Messaging\Enums\MessageStatusEnum;
use App\Modules\Messaging\Models\Contact;
use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Models\Message;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class GenerateMessagesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'messages:generate';

    /**
     * @var string
     */
    protected $description = 'Generate fake contacts, conversations and messages for all channels';

    private Generator $faker;

    public function __construct()
    {
        parent::__construct();
        $this->faker = FakerFactory::create('pt_BR');
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            foreach (ConversationChannelEnum::cases() as $channel) {
                $this->generateForChannel($channel);
            }
        });
    }

    private function generateForChannel(ConversationChannelEnum $channel): void
    {
        /** @var Contact $contact */
        $contact = Contact::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->optional(0.8)->phoneNumber(),
        ]);

        /** @var Conversation $conversation */
        $conversation = Conversation::create([
            'contact_id' => $contact->id,
            'channel' => $channel,
            'has_unread_messages' => true,
            'unread_counts' => 100,
        ]);

        for ($i = 0; $i < 100; $i++) {
            $isInbound = $this->faker->boolean(60);

            Message::create([
                'conversation_id' => $conversation->id,
                'direction' => $isInbound ? MessageDirectionEnum::Inbound : MessageDirectionEnum::Outbound,
                'content' => $this->faker->sentence($this->faker->numberBetween(3, 12)),
                'subject' => $channel === ConversationChannelEnum::Email
                    ? $this->faker->sentence(3)
                    : null,
                'status' => MessageStatusEnum::Sent,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            sleep(1);
        }

        $lastMessage = $conversation->messages()->latest()->first();
        $conversation->update([
            'updated_at' => $lastMessage->created_at,
        ]);
    }
}
