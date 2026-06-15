<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\{
    Database\Eloquent\Factories\HasFactory,
    Database\Eloquent\Model,
    Database\Eloquent\Relations\BelongsTo,
    Database\Eloquent\SoftDeletes,
};
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ChatDiscussion',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'text', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'is_read', type: 'integer', nullable: true, example: 0),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-06-14 12:00:00'),
    ]
)]
#[OA\Schema(
    schema: 'ChatDiscussionWithUser',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'text', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'is_read', type: 'integer', nullable: true, example: 0),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-06-14 12:00:00'),
        new OA\Property(
            property: 'user',
            type: 'object',
            ref: '#/components/schemas/UserName'
        ),
    ]
)]
class ChatDiscussion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table    = 'chats_discussions';
    protected $guarded  = ['id'];
    protected $hidden = [
        'chat_id',
        'created_at',
        'deleted_at',
    ];
    protected $fillable = [
        'chat_id',
        'user_id',
        'text',
        'is_read'
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
