<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

abstract class AuthRequest extends FormRequest
{
    protected const PASSWORD_RULES = [
        'required',
        'string',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected static function passwordRules(): array
    {
        return [
            ...self::PASSWORD_RULES,
            Password::min(8),
        ];
    }
}