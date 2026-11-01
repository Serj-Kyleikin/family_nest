<?php

namespace App\Http\Requests\Api\Expenses\Expenses;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids'              => 'nullable|array',
            'user_ids.*'            => 'integer|distinct',
            'template_group_ids'    => 'nullable|array',
            'template_group_ids.*'  => 'integer|distinct',
            'last_id'               => 'nullable|integer|min:1',
            'date_from'             => 'nullable|date',
            'date_to'               => 'nullable|date',
            'limit'                 => 'required|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.array'                    => 'Неверный тип данных',
            'user_ids.*.integer'                => 'Неверный тип данных',
            'user_ids.*.distinct'               => 'Значения не должны повторяться',
            'template_group_ids.array'          => 'Неверный тип данных',
            'template_group_ids.*.integer'      => 'Неверный тип данных',
            'template_group_ids.*.distinct'     => 'Значения не должны повторяться',
            'last_id.integer'                   => 'Неверный тип данных',
            'last_id.min'                       => 'Значение слишком маленькое',
            'date_from.date'                    => 'Неверный формат даты',
            'date_to.date'                      => 'Неверный формат даты',
            'limit.required'                    => 'Поле обязательно для заполнения',
            'limit.integer'                     => 'Неверный тип данных',
            'limit.min'                         => 'Значение слишком маленькое',
            'limit.max'                         => 'Значение слишком большое',
        ];
    }
}
