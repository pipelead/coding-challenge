<?php

namespace Tests\Feature\Messaging;

use App\Modules\Messaging\Jobs\ProcessMessageSendingJob;
use App\Modules\Messaging\Models\{Conversation, Message, Contact};
use App\Modules\Messaging\Services\ConversationService;
use App\Modules\Messaging\Enums\{ConversationChannelEnum, MessageDirectionEnum, MessageStatusEnum};
use Database\Factories\{ContactFactory, ConversationFactory};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(ConversationService::class);
});

describe('ConversationService', function () {
    describe('getConversationsForChannel', function () {
        it('returns conversations for specific channel', function () {
            $contact = ContactFactory::new()->create();

            ConversationFactory::new()->create([
                'contact_id' => $contact->id,
                'channel' => ConversationChannelEnum::WhatsApp
            ]);

            ConversationFactory::new()->create([
                'contact_id' => $contact->id,
                'channel' => ConversationChannelEnum::Email
            ]);

            $whatsappConversations = $this->service->getConversationsForChannel('whatsapp');
            $emailConversations = $this->service->getConversationsForChannel('email');

            expect($whatsappConversations)->toHaveCount(1)
                ->and($emailConversations)->toHaveCount(1)
                ->and($whatsappConversations->first()->provider)->toBe('whatsapp');
        });

        it('returns empty collection for channel with no conversations', function () {
            $conversations = $this->service->getConversationsForChannel('messenger');

            expect($conversations)->toBeEmpty();
        });
    });

    describe('getConversationWithMessages', function () {
        it('returns conversation with paginated messages', function () {
            /** @var Contact $contact */
            $contact = Contact::factory()->create();
            /** @var Conversation $conversation */
            $conversation = Conversation::factory()->create(['contact_id' => $contact->id]);

            Message::factory()->count(5)->create([
                'conversation_id' => $conversation->id
            ]);

            $result = $this->service->getConversationWithMessages($conversation->id);

            expect($result)->toHaveKey('conversation')
                ->and($result)->toHaveKey('messages')
                ->and($result['conversation']->id)->toBe($conversation->id)
                ->and($result['messages'])->toHaveKey('data')
                ->and($result['messages'])->toHaveCount(5);
        });

        it('throws exception for non-existent conversation', function () {
            expect(fn() => $this->service->getConversationWithMessages(999))
                ->toThrow(ModelNotFoundException::class);
        });
    });

    describe('createMessage', function () {
        it('creates outbound message successfully', function () {
            /** @var Contact $contact */
            $contact = Contact::factory()->create();

            /** @var Conversation $conversation */
            $conversation = Conversation::factory()->create(['contact_id' => $contact->id]);

            $messageData = [
                'content' => 'Test message',
                'subject' => 'Test subject'
            ];

            $message = $this->service->createMessage($conversation->id, $messageData);

            expect($message->content)->toBe('Test message')
                ->and($message->subject)->toBe('Test subject')
                ->and($message->direction)->toBe(MessageDirectionEnum::Outbound)
                ->and($message->status)->toBe(MessageStatusEnum::Pending)
                ->and($message->conversation_id)->toBe($conversation->id);
        });

        it('dispatches job to process message', function () {
            Queue::fake();

            /** @var Contact $contact */
            $contact = Contact::factory()->create();

            /** @var Conversation $conversation */
            $conversation = Conversation::factory()->create(['contact_id' => $contact->id]);

            $messageData = ['content' => 'Test message'];

            $this->service->createMessage($conversation->id, $messageData);

            Queue::assertPushed(ProcessMessageSendingJob::class);
        });
    });

    describe('markAsRead', function () {
        it('marks conversation as read', function () {
            /** @var Contact $contact */
            $contact = Contact::factory()->create();
            /** @var Conversation $conversation */
            $conversation = Conversation::factory()->create([
                'contact_id' => $contact->id,
                'has_unread_messages' => true,
                'unread_counts' => 5,
            ]);

            $this->service->markAsRead($conversation->id);

            $conversation->refresh();
            expect($conversation->unread_counts)->toBe(0);
        });
    });
});
