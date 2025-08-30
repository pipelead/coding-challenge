<?php

namespace App\Modules\Messaging\Repositories;

use App\Modules\Messaging\Enums\MessageDirectionEnum;
use App\Modules\Messaging\Models\Message;
use Illuminate\Support\Facades\DB;
use Throwable;

class MessageRepository
{
    /**
     * @throws Throwable
     */
    public function create(array $data): Message
    {
        return DB::transaction(function () use ($data) {
            /** @var Message $message */
            $message = Message::create($data);

            $conversation = $message->conversation;
            $conversation->touch();

            if ($message->direction === MessageDirectionEnum::Inbound) {
                $conversation->increment('unread_counts');
                $conversation->update(['has_unread_messages' => true]);
            }

            return $message;
        });
    }
}
