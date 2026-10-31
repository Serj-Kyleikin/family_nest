<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'text.required' => 'The field is required',
            'text.string'   => 'Invalid data type',
        ];
    }
}
