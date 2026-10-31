<?php

namespace App\Services\Expenses\Util;

use App\Models\Expenses\ExpenseTemplateGroup;
use App\Exceptions\HandledException;

class ExpenseTemplateGroupRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isExists(int $id, int $userId): void
    {
        $group = ExpenseTemplateGroup::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        throw_unless(
            $group, 
            HandledException::class, 
            'Expense template group is not found', 
            404
        );
    }
}
