<?php

namespace Database\Seeders;

use App\Modules\Messaging\Enums\ConversationChannelEnum;
use App\Modules\Messaging\Models\Contact;
use App\Modules\Messaging\Models\Conversation;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = Contact::all();
        $channels = ConversationChannelEnum::cases();

        foreach ($contacts as $contact) {
            $numConversations = rand(1, 3);
            $selectedChannels = collect($channels)->random($numConversations);

            foreach ($selectedChannels as $channel) {
                Conversation::create([
                    'contact_id' => $contact->id,
                    'channel' => $channel,
                    'has_unread_messages' => rand(0, 1),
                    'unread_counts' => rand(0, 5),
                ]);
            }
        }
    }
}
