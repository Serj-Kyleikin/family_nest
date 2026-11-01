<?php

namespace App\Models;

use App\Exceptions\HandledException;
use App\Models\Family\FamilyGroup;
use App\Models\Family\FamilyGroupLinkRequest;
use App\Services\Family\Enums\FamilyGroupLinkRequestStatusEnum;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

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
#[OA\Schema(
    schema: 'CurrentUser',
    properties: [
        new OA\Property(property: 'id',                 type: 'integer', example: 3),
        new OA\Property(property: 'name',               type: 'string',  example: 'Иван Иванов'),
        new OA\Property(property: 'email',              type: 'string',  example: 'ivan@example.com', format: 'email'),
        new OA\Property(property: 'group_id',           type: 'integer', example: 1, nullable: true),
        new OA\Property(property: 'group',              type: 'object',  ref: '#/components/schemas/FamilyGroup'),
        new OA\Property(property: 'is_pending_request', type: 'boolean', example: false),
        new OA\Property(property: 'link_token',         type: 'string',  example: 'k3J9xQ2mV8aLpR5tZ1wY7cBn4dFhGs6uEoXiAjKq', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'FamilyMember',
    properties: [
        new OA\Property(property: 'id',        type: 'integer', example: 5),
        new OA\Property(property: 'name',      type: 'string',  example: 'Сын', description: 'Отображаемое имя: alias, заданный текущим пользователем, или имя из профиля'),
        new OA\Property(property: 'alias',     type: 'string',  example: 'Сын', nullable: true, description: 'Alias, заданный текущим пользователем'),
        new OA\Property(property: 'group_id',  type: 'integer', example: 1),
        new OA\Property(property: 'group',     type: 'object',  ref: '#/components/schemas/FamilyGroup'),
        new OA\Property(property: 'user_data', type: 'object',  ref: '#/components/schemas/UserData', nullable: true, description: 'Публичные данные участника. Приходит только в карточке участника'),
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

    protected $with = [
        'group',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    #################
    #   Relations   #
    #################

    public function group(): BelongsTo
    {
        return $this->belongsTo(FamilyGroup::class, 'group_id');
    }

    public function userData(): HasOne
    {
        return $this->hasOne(UserData::class, 'user_id');
    }

    public function joinRequest(): HasOne
    {
        return $this->hasOne(FamilyGroupLinkRequest::class, 'user_id')
            ->where('status', FamilyGroupLinkRequestStatusEnum::PENDING->value);
    }

    ###############
    #   Methods   #
    ###############

    public function getGroup(): FamilyGroup
    {
        $group = $this->group;

        throw_unless(
            $group,
            HandledException::class,
            'Group not found',
            Response::HTTP_NOT_FOUND
        );

        return $group;
    }
}