<?php

namespace App\Services\Settings;

use App\Services\{
    Auth\Repositories\UserRepository,
    Settings\Repositories\ExpensePermissionRepository,
    Settings\Util\SettingsExpensesRulesUtil,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class SettingsExpensesService
{
    public function __construct(
        private readonly UserRepository                 $userRepository,
        private readonly ExpensePermissionRepository    $expensePermissionRepository,
    ) {
    }

    public function get(): Collection
    {
        $user       = Auth::user();
        $group      = $user->getGroup();

        $viewerIds  = $this->expensePermissionRepository
            ->getWhere(['owner_id' => $user->id], ['viewer_id'])
            ->pluck('viewer_id');

        return $this->userRepository
            ->getWhere(['group_id' => $group->id], ['id', 'name'])
            ->reject(fn ($member) => $member->id === $user->id)
            ->map(fn ($member) => [
                'id'            => $member->id,
                'name'          => $member->name,
                'is_allowed'    => $viewerIds->contains($member->id),
            ])
            ->values();
    }

    public function update(array $userIds): Collection
    {
        $user       = Auth::user();
        $group      = $user->getGroup();

        $memberIds  = $this->userRepository
            ->getWhere(['group_id' => $group->id], ['id'])
            ->pluck('id')
            ->reject(fn ($id) => $id === $user->id)
            ->values()
            ->all();

        SettingsExpensesRulesUtil::isMembers($userIds, $memberIds);

        $this->expensePermissionRepository->delete(['owner_id' => $user->id]);
        $this->expensePermissionRepository->insert(
            array_map(
                fn (int $viewerId) => ['owner_id' => $user->id, 'viewer_id' => $viewerId],
                array_values(array_unique($userIds))
            )
        );

        return $this->get();
    }
}
