<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class UserDataUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'        => 'sometimes|nullable|string|max:255',
            'family_status' => 'sometimes|nullable|string|max:255',
            'avatar'        => 'sometimes|nullable|string|max:500',
            'birthday'      => 'sometimes|nullable|date_format:Y-m-d|before_or_equal:today',
            'address'       => 'sometimes|nullable|string|max:500',
            'phone'         => 'sometimes|nullable|string|max:32|regex:/^\+?[0-9\s\-\(\)]+$/',
        ];
    }

    public function messages(): array
    {
        return [
            'status.string'             => 'Неверный тип данных',
            'status.max'                => 'Значение слишком длинное',
            'family_status.string'      => 'Неверный тип данных',
            'family_status.max'         => 'Значение слишком длинное',
            'avatar.string'             => 'Неверный тип данных',
            'avatar.max'                => 'Значение слишком длинное',
            'birthday.date_format'      => 'Неверный формат даты',
            'birthday.before_or_equal'  => 'Дата не может быть в будущем',
            'address.string'            => 'Неверный тип данных',
            'address.max'               => 'Значение слишком длинное',
            'phone.string'              => 'Неверный тип данных',
            'phone.max'                 => 'Значение слишком длинное',
            'phone.regex'               => 'Неверный формат телефона',
        ];
    }
}
