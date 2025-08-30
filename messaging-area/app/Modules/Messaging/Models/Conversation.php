<?php

namespace App\Modules\Messaging\Models;

use App\Modules\Messaging\Enums\{ConversationChannelEnum, MessageDirectionEnum};
use Carbon\Carbon;
use Database\Factories\ConversationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

/**
 * @property int $id
 * @property int $contact_id
 * @property ConversationChannelEnum $channel
 * @property bool $has_unread_messages
 * @property int $unread_counts
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Contact $contact
 * @property Message $lastMessage
 * @property HasMany $messages
 */
class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'channel',
        'has_unread_messages',
        'unread_counts',
    ];

    protected $casts = [
        'channel' => ConversationChannelEnum::class,
        'has_unread_messages' => 'boolean',
    ];

    protected static function newFactory(): ConversationFactory
    {
        return ConversationFactory::new();
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest('id');
    }

    public function unreadMessages(): HasMany
    {
        return $this->messages()
            ->where('direction', MessageDirectionEnum::Inbound)
            ->where('status', '!=', 'read');
    }

    public function markAsRead(): void
    {
        if ($this->has_unread_messages) {
            $this->update([
                'has_unread_messages' => false,
                'unread_count' => 0
            ]);

            $this->unreadMessages()->update([
                'status' => 'read',
            ]);
        }
    }
}
