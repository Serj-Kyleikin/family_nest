<?php

namespace App\Services\Expenses\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use App\Models\Expenses\Expense;
use App\Models\Expenses\ExpensePermission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ExpenseRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected Expense $model
    ) {
    }

    public function getForUser(
        int         $userId,
        int|null    $familyGroupId,
        array       $ownerIds,
        array       $templateGroupIds,
        int|null    $lastId,
        int         $limit,
        Carbon|null $dateFrom = null,
        Carbon|null $dateTo = null
    ): Collection {
        $permittedOwners = ExpensePermission::query()
            ->select('owner_id')
            ->where('viewer_id', $userId);

        $lastCreatedAt = $lastId === null
            ? null
            : $this->model->newQuery()->withTrashed()->whereKey($lastId)->value('created_at');

        return $this->model->query()
            ->join('users', 'users.id', '=', 'expenses.user_id')
            ->leftJoin('expense_template_groups', function ($join) {
                $join->on('expense_template_groups.id', '=', 'expenses.template_group_id')
                    ->on(function ($join) {
                        $join->on('expense_template_groups.user_id', '=', 'expenses.user_id')
                            ->orWhereNull('expense_template_groups.user_id');
                    });
            })
            ->where(function ($query) use ($userId, $familyGroupId, $permittedOwners) {
                $query->where('expenses.user_id', $userId)
                    ->when($familyGroupId !== null, function ($query) use ($familyGroupId, $permittedOwners) {
                        $query->orWhere(function ($query) use ($familyGroupId, $permittedOwners) {
                            $query->where('users.group_id', $familyGroupId)
                                ->whereIn('expenses.user_id', $permittedOwners);
                        });
                    });
            })
            ->when($ownerIds !== [], fn ($query) => $query->whereIn('expenses.user_id', $ownerIds))
            ->when($templateGroupIds !== [], fn ($query) => $query->whereIn('expenses.template_group_id', $templateGroupIds))
            ->when($lastId !== null, function ($query) use ($lastId, $lastCreatedAt) {
                if ($lastCreatedAt === null) {
                    $query->where('expenses.id', '<', $lastId);

                    return;
                }

                $query->where(function ($query) use ($lastId, $lastCreatedAt) {
                    $query->where('expenses.created_at', '<', $lastCreatedAt)
                        ->orWhere(function ($query) use ($lastId, $lastCreatedAt) {
                            $query->where('expenses.created_at', $lastCreatedAt)
                                ->where('expenses.id', '<', $lastId);
                        });
                });
            })
            ->when($dateFrom !== null, fn ($query) => $query->where('expenses.created_at', '>=', $dateFrom))
            ->when($dateTo !== null, fn ($query) => $query->where('expenses.created_at', '<', $dateTo))
            ->orderBy('expenses.created_at', 'desc')
            ->orderBy('expenses.id', 'desc')
            ->limit($limit)
            ->get([
                'expenses.id',
                'expenses.user_id',
                'users.name as user_name',
                'expenses.template_group_id',
                'expense_template_groups.name as template_group_name',
                'expenses.name',
                'expenses.amount',
                'expenses.created_at',
            ]);
    }
}
