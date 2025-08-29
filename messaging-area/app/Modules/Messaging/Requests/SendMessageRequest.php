<?php

namespace App\Modules\Messaging\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:5000',
            'subject' => 'nullable|string|max:255',
            'reply_to_message_id' => 'nullable|exists:messages,id',
        ];
    }
}
