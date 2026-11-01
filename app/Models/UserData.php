<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserData',
    properties: [
        new OA\Property(property: 'user_id',       type: 'integer', example: 3),
        new OA\Property(property: 'status',        type: 'string',  example: 'На даче до воскресенья',   nullable: true),
        new OA\Property(property: 'family_status', type: 'string',  example: 'Мама',                     nullable: true),
        new OA\Property(property: 'avatar',        type: 'string',  example: '/storage/avatars/3.jpg',   nullable: true),
        new OA\Property(property: 'birthday',      type: 'string',  example: '1990-05-17',               nullable: true, format: 'date'),
        new OA\Property(property: 'address',       type: 'string',  example: 'Москва, ул. Ленина, 1',    nullable: true),
        new OA\Property(property: 'phone',         type: 'string',  example: '+7 900 123-45-67',         nullable: true),
    ]
)]
class UserData extends Model
{
    use HasFactory;

    protected $table = 'user_data';

    protected $fillable = [
        'user_id',
        'status',
        'family_status',
        'avatar',
        'birthday',
        'address',
        'phone',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
