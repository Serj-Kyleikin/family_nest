<?php

namespace App\Models\Family;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FamilyGroupLink',
    properties: [
        new OA\Property(property: 'id',        type: 'integer', example: 1),
        new OA\Property(property: 'group_id',  type: 'integer', example: 1),
        new OA\Property(property: 'token',     type: 'string',  example: 'k3J9xQ2mV8aLpR5tZ1wY7cBn4dFhGs6uEoXiAjKq'),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
    ]
)]
class FamilyGroupLink extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'token',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
