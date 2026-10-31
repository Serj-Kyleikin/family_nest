<?php

namespace App\Services\Expenses\Util;

use App\Models\Expenses\ExpenseTemplate;
use App\Exceptions\HandledException;

class ExpenseTemplateRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isExists(int $id, int $userId): void
    {
        $template = ExpenseTemplate::query()
            ->where('id', $id)
            ->whereHas('templateGroup', fn ($query) => $query->where('user_id', $userId))
            ->first();

        throw_unless(
            $template, 
            HandledException::class, 
            'Expense template is not found', 
            404
        );
    }
}
