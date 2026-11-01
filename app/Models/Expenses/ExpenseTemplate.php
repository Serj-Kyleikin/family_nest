<?php

namespace App\Models\Expenses;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ExpenseTemplate extends Model
{
    use HasFactory;

    protected $table = 'expense_templates';

    public $timestamps = false;

    protected $fillable = [
        'template_group_id',
        'name',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'     => 'boolean',
            'is_customized' => 'boolean',
        ];
    }

    public function scopeWithGroupCopy(Builder $query, int|null $familyGroupId): Builder
    {
        return $query
            ->leftJoin('expense_templates_user_groups', function ($join) use ($familyGroupId) {
                $join->on('expense_templates_user_groups.expense_template_id', '=', 'expense_templates.id')
                    ->where('expense_templates_user_groups.group_id', $familyGroupId);
            })
            ->select([
                'expense_templates.id',
                'expense_templates.template_group_id',
                DB::raw('coalesce(expense_templates_user_groups.name, expense_templates.name) as name'),
                DB::raw('case when expense_templates_user_groups.id is null then coalesce(expense_templates.price, 0) else coalesce(expense_templates_user_groups.price, 0) end as price'),
                DB::raw('coalesce(expense_templates_user_groups.is_active, expense_templates.is_active) as is_active'),
                DB::raw('expense_templates_user_groups.id is not null as is_customized'),
            ]);
    }

    public function templateGroup(): BelongsTo
    {
        return $this->belongsTo(ExpenseTemplateGroup::class, 'template_group_id');
    }
}
