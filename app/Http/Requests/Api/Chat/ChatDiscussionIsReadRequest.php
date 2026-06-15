<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatDiscussionIsReadRequest extends FormRequest
{
    public string $ids;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "ids" => "required|array"
        ];
    }

    public function messages(): array
    {
        return [
            'ids' => [
                'required'  => 'The field is required',
                'array'     => 'Invalid data type'
            ]
        ];
    }
}
