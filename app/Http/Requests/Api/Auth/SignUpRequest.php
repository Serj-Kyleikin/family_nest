<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Validation\Rules\Password;

class SignUpRequest extends AuthRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:30',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:20',
                'unique:users,email',
            ],
            'password' => self::passwordRules(),
        ];
    }

    public function messages(): array
    {
        return [
            'name' => [
                'required'  => 'The field is required',
                'string'    => 'Invalid data type',
                'min'       => 'The field min size is 2',
                'max'       => 'The field max size is 30',
            ],
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