<?php

namespace App\Models\Family;

use App\Models\User;
use App\Services\Family\Enums\FamilyGroupLinkRequestStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FamilyGroupLinkRequest',
    properties: [
        new OA\Property(property: 'id',      type: 'integer', example: 1),
        new OA\Property(property: 'link_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 5),
        new OA\Property(property: 'status',  type: 'string',  example: 'pending', enum: FamilyGroupLinkRequestStatusEnum::class),
        new OA\Property(property: 'user',    type: 'object',  ref: '#/components/schemas/UserName'),
    ]
)]
class FamilyGroupLinkRequest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $attributes = [
        'status' => FamilyGroupLinkRequestStatusEnum::PENDING->value,
    ];

    protected $fillable = [
        'link_id',
        'user_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => FamilyGroupLinkRequestStatusEnum::class,
        ];
    }

    #################
    #   Relations   #
    #################

    public function link(): BelongsTo
    {
        return $this->belongsTo(FamilyGroupLink::class, 'link_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
