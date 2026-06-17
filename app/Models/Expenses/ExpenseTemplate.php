<?php

namespace App\Models\Expenses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseTemplate extends Model
{
    use HasFactory;

    protected $table = 'expense_templates';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function templateGroup(): BelongsTo
    {
        return $this->belongsTo(ExpenseTemplateGroup::class, 'group_id');
    }
}
