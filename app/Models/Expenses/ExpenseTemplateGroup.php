<?php

namespace App\Models\Expenses;

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

    public function templates(): HasMany
    {
        return $this->hasMany(ExpenseTemplate::class, 'group_id');
    }
}
