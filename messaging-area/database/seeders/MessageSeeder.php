<?php

namespace Database\Seeders;

use App\Modules\Messaging\Enums\{ConversationChannelEnum, MessageDirectionEnum, MessageStatusEnum};
use App\Modules\Messaging\Models\{Conversation, Message};
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $conversations = Conversation::with('contact')->get();

        /** @var Conversation $conversation */
        foreach ($conversations as $conversation) {
            $messageCount = rand(3, 15);

            for ($i = 0; $i < $messageCount; $i++) {
                $isInbound = $i == 0 || rand(0, 1);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'direction' => $isInbound ? MessageDirectionEnum::Inbound : MessageDirectionEnum::Outbound,
                    'content' => $this->getContent($isInbound),
                    'subject' => $this->getSubject($conversation->channel),
                    'status' => $this->getStatus($isInbound),
                ]);
            }

            $this->updateConversation($conversation);
        }
    }

    private function getContent(bool $isInbound): string
    {
        $inboundMessages = [
            'Preciso de ajuda com meu pedido',
            'Qual o horário de funcionamento?',
            'Meu produto não chegou ainda',
            'Como faço para cancelar?',
            'Obrigado pela ajuda!',
        ];

        $outboundMessages = [
            'Olá! Como posso ajudá-lo?',
            'Vou verificar isso para você',
            'Seu pedido foi processado',
            'Está tudo resolvido',
            'Qualquer dúvida, é só avisar!',
        ];

        $messages = $isInbound ? $inboundMessages : $outboundMessages;
        return $messages[array_rand($messages)];
    }

    private function getSubject(ConversationChannelEnum $channel): ?string
    {
        if ($channel !== ConversationChannelEnum::Email) {
            return null;
        }

        $subjects = [
            'Pedido confirmado #12345',
            'Sua dúvida foi respondida',
            'Produto enviado',
            'Avalie sua compra',
            'Promoção especial',
        ];

        return $subjects[array_rand($subjects)];
    }

    private function getStatus(bool $isInbound): MessageStatusEnum
    {
        if ($isInbound) {
            return rand(0, 1) ? MessageStatusEnum::Read : MessageStatusEnum::Delivered;
        }

        $statuses = [MessageStatusEnum::Sent, MessageStatusEnum::Delivered, MessageStatusEnum::Read];
        return $statuses[array_rand($statuses)];
    }

    private function updateConversation(Conversation $conversation): void
    {
        $unreadCount = $conversation->messages()
            ->where('direction', MessageDirectionEnum::Inbound)
            ->where('status', '!=', MessageStatusEnum::Read)
            ->count();

        $conversation->update([
            'unread_counts' => $unreadCount,
            'has_unread_messages' => $unreadCount > 0,
            'updated_at' => $conversation->messages()->latest()->first()->created_at ?? now(),
        ]);
    }
}
