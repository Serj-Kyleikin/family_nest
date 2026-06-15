<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserName',
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            example: 3,
            description: 'Id пользователя'
        ),

        new OA\Property(
            property: 'name',
            type: 'string',
            example: 'Кира Найтли',
            description: 'Имя пользователя'
        ),
    ]
)]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}