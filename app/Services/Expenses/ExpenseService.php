<?php

namespace App\Services\Expenses;

use App\Services\{
    Expenses\Repositories\ExpenseRepository,
    Expenses\Repositories\ExpenseTemplateRepository,
    Expenses\Util\ExpenseRulesUtil,
};
use App\Exceptions\HandledException;
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class ExpenseService
{
    public function __construct(
        private readonly ExpenseRepository              $expenseRepository,
        private readonly ExpenseTemplateRepository      $expenseTemplateRepository,
    ) {
    }

    public function list(int|null $groupId = null): Collection
    {
        $userId = Auth::id();

        # ToDo: add by date
        return $this->expenseRepository->getForUser($userId, $groupId);
    }

    public function create(int|null $templateId, string|null $name, string $amount, int|null $groupId = null): void 
    {
        $userId = Auth::id();

        if (is_int($templateId)) {

            $template = $this->expenseTemplateRepository->firstForUser($templateId, $userId, true);
            throw_unless(
                $template, 
                HandledException::class, 
                'Template not found', 
                404
            );

            $name       = $template->name;
            $groupId    = $groupId ? $groupId : $template->group_id;
        }

        $this->expenseRepository->create([
            'user_id'       => $userId,
            'group_id'      => $groupId,
            'template_id'   => $templateId,
            'name'          => $name,
            'amount'        => $amount,
        ]);
    }

    public function update(int $id, ?int $templateId, ?string $name, string $amount, ?int $groupId = null): void 
    {
        $userId = Auth::id();

        ExpenseRulesUtil::isExists($id, $userId);

        if (is_int($templateId)) {

            $template = $this->expenseTemplateRepository->firstForUser($templateId, $userId, true);
            throw_unless(
                $template, 
                HandledException::class, 
                'Template not found', 
                404
            );

            $name       = $template->name;
            $groupId    = $groupId ? $groupId : $template->group_id;
        }

        $data = [
            'template_id'   => $templateId,
            'name'          => $name,
            'amount'        => $amount,
        ];

        if (filled($groupId)) {
            $data['group_id'] = $groupId;
        }

        $this->expenseRepository->update(['id' => $id, 'user_id' => $userId], $data);
    }

    public function delete(int $id): void
    {
        $userId = Auth::id();

        ExpenseRulesUtil::isExists($id, $userId);
        $this->expenseRepository->delete(['id' => $id, 'user_id' => $userId]);
    }
}
