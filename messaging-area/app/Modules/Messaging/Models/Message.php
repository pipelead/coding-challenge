<?php

namespace App\Modules\Messaging\Models;

use Database\Factories\MessageFactory;
use App\Modules\Messaging\Enums\{MessageDirectionEnum, MessageStatusEnum};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $conversation_id
 * @property ?int $reply_to_message_id
 * @property MessageDirectionEnum $direction
 * @property string $content
 * @property ?string $subject
 * @property MessageStatusEnum $status
 * @property ?string $error_message
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Conversation $conversation
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'reply_to_message_id',
        'direction',
        'content',
        'subject',
        'status',
        'error_message',
    ];

    protected $casts = [
        'direction' => MessageDirectionEnum::class,
        'status' => MessageStatusEnum::class
    ];

    protected static function newFactory(): MessageFactory
    {
        return MessageFactory::new();
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
