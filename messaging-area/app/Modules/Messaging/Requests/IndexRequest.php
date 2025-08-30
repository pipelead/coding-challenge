<?php

namespace App\Modules\Messaging\Requests;

use App\Modules\Messaging\Enums\ConversationChannelEnum;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel' => ['sometimes', 'string', 'in:' . implode(',', ConversationChannelEnum::getValues())],
            'conversation_id' => 'nullable|integer|exists:conversations,id',
        ];
    }
}
