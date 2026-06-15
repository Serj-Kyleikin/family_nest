<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\{
    Database\Eloquent\Factories\HasFactory,
    Database\Eloquent\Model,
    Database\Eloquent\Relations\BelongsTo,
};

class ChatMembers extends Model
{
    use HasFactory;

    protected $table    = 'chats_members';
    protected $guarded  = ['id'];
    public $timestamps  = false;
    protected $hidden = [
        'chat_id',
        'user_id',
    ];
    protected $fillable = [
        'chat_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
