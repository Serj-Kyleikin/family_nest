<?php

namespace App\Http\Requests\Api\Family\Group;

use Illuminate\Foundation\Http\FormRequest;

class FamilyLinkRequestStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'    => 'Поле обязательно для заполнения',
            'token.string'      => 'Неверный тип данных',
        ];
    }
}
