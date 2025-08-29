<?php

namespace Database\Factories;

use App\Modules\Messaging\Enums\{ConversationChannelEnum, MessageDirectionEnum, MessageStatusEnum};
use App\Modules\Messaging\Models\{Conversation, Message};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $direction = $this->faker->randomElement(MessageDirectionEnum::cases());

        return [
            'conversation_id' => Conversation::factory(),
            'direction' => $direction,
            'content' => $this->getContent(),
            'subject' => null,
            'status' => $this->getStatus($direction),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Message $message) {
            if ($message->conversation->channel === ConversationChannelEnum::Email) {
                $message->subject = $this->getEmailSubject();
            }
        });
    }

    private function getContent(): string
    {
        $messages = [
            'Olá! Como posso ajudar?',
            'Obrigado pelo contato!',
            'Seu pedido foi processado.',
            'Tem alguma dúvida?',
            'Perfeito! Resolvido.',
            'Preciso de mais informações.',
            'Vou verificar para você.',
            'Tudo certo por aqui.',
            'Qualquer coisa é só avisar!',
            'Tenha um ótimo dia!',
        ];

        return $this->faker->randomElement($messages);
    }

    private function getStatus(MessageDirectionEnum $direction): MessageStatusEnum
    {
        if ($direction === MessageDirectionEnum::Inbound) {
            return $this->faker->randomElement([
                MessageStatusEnum::Delivered,
                MessageStatusEnum::Read,
            ]);
        }

        return $this->faker->randomElement([
            MessageStatusEnum::Sent,
            MessageStatusEnum::Delivered,
            MessageStatusEnum::Read,
        ]);
    }

    private function getEmailSubject(): string
    {
        $subjects = [
            'Pedido confirmado #' . rand(10000, 99999),
            'Sua dúvida foi respondida',
            'Produto enviado',
            'Avalie sua compra',
            'Promoção especial',
            'Suporte técnico',
            'Confirmação de cadastro',
            'Recuperação de senha',
            'Newsletter semanal',
            'Lembrete importante',
        ];

        return $subjects[array_rand($subjects)];
    }
}
