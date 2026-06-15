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

    public function messages(): array
    {
        return [
            'email' => [
                'required'  => 'The field is required',
                'string'    => 'Invalid data type',
                'email'     => 'The field must be email format',
                'max'       => 'The field max size is 30',
                'unique'    => 'The field must be unique',
            ],
            'password' => [
                'required'  => 'The field is required',
                'string'    => 'Invalid data type',
                'min'       => 'The field min size is 8',
            ],
        ];
    }
}