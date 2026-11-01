<?php

namespace App\Http\Requests\Api\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SettingsExpensesUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids'      => 'present|array',
            'user_ids.*'    => 'integer|distinct',
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.present'          => 'Поле обязательно для заполнения',
            'user_ids.array'            => 'Неверный тип данных',
            'user_ids.*.integer'        => 'Неверный тип данных',
            'user_ids.*.distinct'       => 'Значения не должны повторяться',
        ];
    }
}
