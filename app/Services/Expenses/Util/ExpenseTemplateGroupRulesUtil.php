<?php

namespace App\Services\Expenses\Util;

use App\Models\Expenses\ExpenseTemplateGroup;
use App\Exceptions\HandledException;

class ExpenseTemplateGroupRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isExists(int $id): void
    {
        $group = ExpenseTemplateGroup::where('id', $id)->first();

        throw_unless(
            $group, 
            HandledException::class, 
            'Expense template group is not found', 
            404
        );
    }
}
