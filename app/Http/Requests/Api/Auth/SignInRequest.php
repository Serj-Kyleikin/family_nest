<?php

namespace App\Http\Requests\Api\Auth;

class SignInRequest extends AuthRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => self::passwordRules(),
        ];
    }
}