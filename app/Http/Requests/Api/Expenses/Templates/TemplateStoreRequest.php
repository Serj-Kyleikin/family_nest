<?php

namespace App\Http\Requests\Api\Expenses\Templates;

use Illuminate\Foundation\Http\FormRequest;

class TemplateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
