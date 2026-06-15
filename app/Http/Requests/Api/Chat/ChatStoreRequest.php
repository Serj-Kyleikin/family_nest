<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatStoreRequest extends FormRequest
{
    public int $user_to_id;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "user_to_id" => "required|integer"
        ];
    }

    public function messages(): array
    {
        return [
            'user_to_id' => [
                'required'  => 'The field is required',
                'integer'   => 'Invalid data type',
            ]
        ];
    }
}
