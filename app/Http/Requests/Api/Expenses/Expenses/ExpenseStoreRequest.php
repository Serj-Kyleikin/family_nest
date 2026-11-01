<?php

namespace App\Http\Requests\Api\Expenses\Expenses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ExpenseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_id'       => 'nullable|integer',
            'template_group_id' => 'nullable|integer',
            'name'              => 'nullable|string|min:2|max:20',
            'amount'            => 'required|numeric|decimal:0,2',
            'date'              => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'date.date' => 'Неверный формат даты',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $templateId = $this->input('template_id');
            $name = $this->input('name');

            if ($templateId === null && $name === null) {
                $validator->errors()->add('name', 'Name is required when template_id is not provided.');
            }
        });
    }
}
