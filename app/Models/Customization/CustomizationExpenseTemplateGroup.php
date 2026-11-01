<?php

namespace App\Models\Customization;

use App\Models\Expenses\ExpenseTemplateGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomizationExpenseTemplateGroup extends Model
{
    use HasFactory;

    protected $table = 'customization_expense_template_groups';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'expense_template_group_id',
        'priority',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function expenseTemplateGroup(): BelongsTo
    {
        return $this->belongsTo(ExpenseTemplateGroup::class, 'expense_template_group_id');
    }
}
