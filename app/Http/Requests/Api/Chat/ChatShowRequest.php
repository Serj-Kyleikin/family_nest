<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'before_id' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'before_id.integer' => 'Invalid data type',
            'before_id.min'     => 'Invalid value',
        ];
    }
}
