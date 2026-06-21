<?php

namespace App\Services\Expenses\Util;

use App\Models\Expenses\Expense;
use App\Exceptions\HandledException;

class ExpenseRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isExists(int $id): void
    {
        $expense = Expense::where('id', $id)->first();
        throw_unless(
            $expense, 
            HandledException::class, 
            'Expense is not found', 
            404
        );
    }
}
