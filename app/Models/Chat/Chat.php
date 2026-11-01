<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    SoftDeletes,
    Model,
    Relations\HasMany,
    Relations\HasOne,
    Relations\BelongsToMany,
};
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ChatWithDiscussionsAndMembers',
    properties: [
        new OA\Property(
            property: 'id', 
            type: 'integer', 
            example: 1
        ),
        new OA\Property(
            property: 'members',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/UserName')
        ),
        new OA\Property(
            property: 'discussions',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ChatDiscussion')
        ),
        new OA\Property(
            property: 'has_more',
            type: 'boolean',
            example: true,
            description: 'Есть ли более старые сообщения, не вошедшие в ответ'
        ),
    ]
)]
#[OA\Schema(
    schema: 'ChatWithLastDiscussionAndMembers',
    properties: [
        new OA\Property(
            property: 'id', 
            type: 'integer', 
            example: 1
        ),
        new OA\Property(
            property: 'members',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/UserName')
        ),
        new OA\Property(
            property: 'lastDiscussion',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ChatDiscussion')
        ),
        new OA\Property(
            property: 'unread_count',
            type: 'integer',
            example: 3,
            description: 'Количество непрочитанных сообщений собеседника в чате'
        ),
    ]
)]
class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function members(): hasMany
    {
        return $this->hasMany(ChatMembers::class, 'chat_id', 'id');
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(ChatDiscussion::class);
    }

    public function lastDiscussion(): HasOne
    {
        return $this->hasOne(ChatDiscussion::class, 'chat_id', 'id')
            ->orderByDesc('id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chats_members', 'chat_id', 'user_id');
    }
}
