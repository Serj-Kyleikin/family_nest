<?php

namespace App\Models\Expenses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'template_group_id',
        'template_id',
        'name',
        'amount',
        'created_at',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExpenseTemplate::class, 'template_id');
    }

    public function templateGroup(): BelongsTo
    {
        return $this->belongsTo(ExpenseTemplateGroup::class, 'template_group_id');
    }
}
