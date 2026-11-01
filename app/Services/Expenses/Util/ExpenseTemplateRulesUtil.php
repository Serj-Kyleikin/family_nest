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

    /**
     * @throws \Throwable
     */
    public static function isSameGroup(ExpenseTemplate $template, int $templateGroupId): void
    {
        throw_unless(
            (int) $template->template_group_id === $templateGroupId,
            HandledException::class,
            'Expense template group does not match',
            422
        );
    }

    /**
     * @throws \Throwable
     */
    public static function isNotNull(ExpenseTemplate|null $template): void
    {
        throw_unless(
            $template, 
            HandledException::class, 
            'Expense template is not found', 
            404
        );
    }
}
