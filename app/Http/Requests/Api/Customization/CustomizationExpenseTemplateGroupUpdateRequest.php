<?php

namespace App\Http\Requests\Api\Customization;

use Illuminate\Foundation\Http\FormRequest;

class CustomizationExpenseTemplateGroupUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_template_group_id' => 'required|integer',
            'priority'                  => 'required|integer|min:0|max:65535',
        ];
    }

    public function messages(): array
    {
        return [
            'expense_template_group_id.required'    => 'Поле обязательно для заполнения',
            'expense_template_group_id.integer'     => 'Неверный тип данных',
            'priority.required'                     => 'Поле обязательно для заполнения',
            'priority.integer'                      => 'Неверный тип данных',
            'priority.min'                          => 'Значение слишком маленькое',
            'priority.max'                          => 'Значение слишком большое',
        ];
    }
}
