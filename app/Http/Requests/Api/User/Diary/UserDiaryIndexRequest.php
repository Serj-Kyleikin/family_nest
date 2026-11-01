<?php

namespace App\Http\Requests\Api\User\Diary;

use Illuminate\Foundation\Http\FormRequest;

class UserDiaryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids'      => 'nullable|array',
            'user_ids.*'    => 'integer|distinct',
            'date_from'     => 'nullable|date_format:Y-m-d',
            'date_to'       => 'nullable|date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.array'            => 'Неверный тип данных',
            'user_ids.*.integer'        => 'Неверный тип данных',
            'user_ids.*.distinct'       => 'Значения не должны повторяться',
            'date_from.date_format'     => 'Неверный формат даты',
            'date_to.date_format'       => 'Неверный формат даты',
        ];
    }
}
