<?php

namespace App\Services\Expenses\Util;

use App\Models\Expenses\Expense;
use App\Exceptions\HandledException;

class ExpenseRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isExists(int $id, int $userId): void
    {
        $expense = Expense::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        throw_unless(
            $expense, 
            HandledException::class, 
            'Expense is not found', 
            404
        );
    }
}
