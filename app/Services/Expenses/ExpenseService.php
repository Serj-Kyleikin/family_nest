<?php

namespace App\Services\Expenses;

use App\Services\{
    Expenses\Repositories\ExpenseRepository,
    Expenses\Repositories\ExpenseTemplateRepository,
    Expenses\Util\ExpenseRulesUtil,
};
use App\Exceptions\HandledException;
use App\SharedKernel\Checkers\DateChecker;
use Illuminate\{
    Support\Carbon,
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

    public function list(
        int             $limit,
        int|null        $lastId = null,
        array           $userIds = [],
        array           $templateGroupIds = [],
        Carbon|null     $dateFrom = null,
        Carbon|null     $dateTo = null
    ): Collection {
        DateChecker::isFromNotAfterTo($dateFrom, $dateTo);

        $user = Auth::user();

        return $this->expenseRepository->getForUser(
            $user->id,
            $user->group_id,
            $userIds,
            $templateGroupIds,
            $lastId,
            $limit,
            $dateFrom?->copy()->setTimezone(config('app.timezone')),
            $dateTo?->copy()->setTimezone(config('app.timezone'))
        );
    }

    public function create(int|null $templateId, string|null $name, string $amount, int|null $templateGroupId = null, Carbon|null $date = null): void 
    {
        $userId = Auth::id();

        if (is_int($templateId)) {

            $template = $this->expenseTemplateRepository->firstForUser($templateId, $userId, Auth::user()->group_id, true);
            throw_unless(
                $template, 
                HandledException::class, 
                'Template not found', 
                404
            );

            $name               = $template->name;
            $templateGroupId    = $templateGroupId ? $templateGroupId : $template->template_group_id;
        }

        $data = [
            'user_id'           => $userId,
            'template_group_id' => $templateGroupId,
            'template_id'       => $templateId,
            'name'              => $name,
            'amount'            => $amount,
        ];

        if ($date) {
            $data['created_at'] = $date->copy()->setTimezone(config('app.timezone'));
        }

        $this->expenseRepository->create($data);
    }

    public function update(int $id, ?int $templateId, ?string $name, string $amount, ?int $templateGroupId = null, ?Carbon $date = null): void 
    {
        $userId = Auth::id();
        ExpenseRulesUtil::isExists($id, $userId);

        if(is_int($templateId)) {

            $template           = $this->expenseTemplateRepository
                                       ->firstForUser($templateId, $userId, Auth::user()->group_id, true);
                                throw_unless(
                                    $template, 
                                    HandledException::class, 
                                    'Template not found', 
                                    404
                                );

            $name               = $template->name;
            $templateGroupId    = $templateGroupId ? $templateGroupId : $template->template_group_id;
        }

        $data = [
            'template_id'   => $templateId,
            'name'          => $name,
            'amount'        => $amount,
        ];

        if(filled($templateGroupId)) {
            $data['template_group_id'] = $templateGroupId;
        }

        if ($date) {
            $data['created_at'] = $date->copy()->setTimezone(config('app.timezone'));
        }

        $this->expenseRepository->update(
            ['id' => $id, 'user_id' => $userId], 
            $data
        );
    }

    public function delete(int $id): void
    {
        $userId = Auth::id();

        ExpenseRulesUtil::isExists($id, $userId);
        $this->expenseRepository->delete([
            'id'        => $id,
            'user_id'   => $userId
        ]);
    }
}
