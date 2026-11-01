<?php

namespace App\Models\Expenses;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseTemplateGroup extends Model
{
    use HasFactory;

    protected $table = 'expense_template_groups';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function scopeWithUserPriority(Builder $query, int $userId): Builder
    {
        return $query
            ->leftJoin('customization_expense_template_groups', function ($join) use ($userId) {
                $join->on('customization_expense_template_groups.expense_template_group_id', '=', 'expense_template_groups.id')
                    ->where('customization_expense_template_groups.user_id', $userId);
            })
            ->select([
                'expense_template_groups.id',
                'expense_template_groups.user_id',
                'expense_template_groups.name',
                'customization_expense_template_groups.priority',
            ])
            ->orderByRaw('customization_expense_template_groups.priority is null')
            ->orderBy('customization_expense_template_groups.priority')
            ->orderBy('expense_template_groups.id');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(ExpenseTemplate::class, 'template_group_id');
    }
}
