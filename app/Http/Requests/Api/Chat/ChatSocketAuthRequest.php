<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatSocketAuthRequest extends FormRequest
{
    public string $socket_id;
    public string $channel_name;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "socket_id"     => ["required", "string", "regex:/^\d+\.\d+$/"],
            "channel_name"  => "required|string",
        ];
    }

    public function messages(): array
    {
        return [
            'socket_id' => [
                'required'  => 'The field is required',
                'string'    => 'Invalid data type',
                'regex'     => 'Invalid socket id',
            ],
            'channel_name' => [
                'required'  => 'The field is required',
                'string'    => 'Invalid data type',
            ],
        ];
    }
}
