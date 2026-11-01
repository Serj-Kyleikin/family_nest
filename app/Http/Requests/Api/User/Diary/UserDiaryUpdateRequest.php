<?php

namespace App\Http\Requests\Api\User\Diary;

use Illuminate\Foundation\Http\FormRequest;

class UserDiaryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text'      => 'required|string|max:1000',
            'date'      => 'required|date_format:Y-m-d H:i',
            'date_end'  => 'nullable|date_format:Y-m-d H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'text.required'         => 'Поле обязательно для заполнения',
            'text.string'           => 'Неверный тип данных',
            'text.max'              => 'Текст слишком длинный',
            'date.required'         => 'Поле обязательно для заполнения',
            'date.date_format'      => 'Неверный формат даты и времени',
            'date_end.date_format'  => 'Неверный формат даты и времени',
        ];
    }
}
