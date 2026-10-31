<?php

namespace App\Models\Family;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FamilyGroup',
    properties: [
        new OA\Property(property: 'id',         type: 'integer', example: 1),
        new OA\Property(property: 'name',       type: 'string',  example: 'Ивановы',             nullable: true),
        new OA\Property(property: 'created_at', type: 'string',  example: '2026-06-14 12:00:00', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string',  example: '2026-06-14 12:00:00', format: 'date-time'),
    ]
)]
class FamilyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}