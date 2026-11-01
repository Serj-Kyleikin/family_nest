<?php

namespace App\Models\Diary;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
        'date',
        'date_end',
    ];
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'date'      => 'datetime:Y-m-d H:i',
            'date_end'  => 'datetime:Y-m-d H:i',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
