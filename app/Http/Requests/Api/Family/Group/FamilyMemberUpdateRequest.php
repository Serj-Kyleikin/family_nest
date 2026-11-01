<?php

namespace App\Http\Requests\Api\Family\Group;

use Illuminate\Foundation\Http\FormRequest;

class FamilyMemberUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'present|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.present'  => 'Поле обязательно для заполнения',
            'name.string'   => 'Неверный тип данных',
            'name.max'      => 'Значение слишком длинное',
        ];
    }
}
