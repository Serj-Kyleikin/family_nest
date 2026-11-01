<?php

namespace App\Http\Requests\Api\Expenses\Templates;

use Illuminate\Foundation\Http\FormRequest;

class TemplateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_group_id' => 'required|integer',
            'name'              => 'required|string|max:255',
            'price'             => 'nullable|numeric|decimal:0,2|min:0',
            'is_active'         => 'boolean',
        ];
    }
}
