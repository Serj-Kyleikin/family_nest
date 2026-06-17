<?php

namespace App\Http\Requests\Api\Expenses\TemplateGroups;

use Illuminate\Foundation\Http\FormRequest;

class TemplateGroupStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}

