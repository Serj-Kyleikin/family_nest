<?php

namespace App\Services\Expenses\Util;

use App\Models\Expenses\ExpenseTemplate;
use App\Exceptions\HandledException;

class ExpenseTemplateRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isExists(int $id): void
    {
        $template = ExpenseTemplate::where('id', $id)->first();
        throw_unless(
            $template, 
            HandledException::class, 
            'Expense template is not found', 
            404
        );
    }
}
