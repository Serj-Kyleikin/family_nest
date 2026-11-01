<?php

namespace App\Models\Expenses;

use App\Models\Family\FamilyGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseTemplateUserGroup extends Model
{
    use HasFactory;

    protected $table = 'expense_templates_user_groups';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'expense_template_id',
        'name',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(FamilyGroup::class, 'group_id');
    }

    public function expenseTemplate(): BelongsTo
    {
        return $this->belongsTo(ExpenseTemplate::class, 'expense_template_id');
    }
}
