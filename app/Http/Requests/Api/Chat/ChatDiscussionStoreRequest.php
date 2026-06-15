<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatDiscussionStoreRequest extends FormRequest
{
    public string $text;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "text" => "string",
        ];
    }

    public function messages(): array
    {
        return [
            'text' => [
                'string' => 'Invalid data type'
            ],
        ];
    }
}
